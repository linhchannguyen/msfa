<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PasswordReissue\SendMailPasswordReissuerRequest;
use App\Mail\PasswordReissueMail;
use App\Jobs\SendEmail;
use App\Services\PasswordReissueService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class PasswordReissueController extends Controller
{
    private $service;
    protected $j = 1;

    public function __construct(PasswordReissueService $service)
    {
        $this->service = $service;
        $this->middleware('role.has:' . config('role.MASTER_MG.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\Get(
     *      path="/api/password-reissue",
     *      operationId="getPasswordReissue",
     *      tags={"I02-S01"},
     *      summary="Password Reissue",
     *      description="Get List Password Reissue",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="password_change",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="user_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                           "records": {
     *                               {
     *                                   "user_cd": "10001",
     *                                   "user_name": "西嶋 洋明",
     *                                   "user_post_type_name": "営業部門長",
     *                                   "org_label": "営業部門",
     *                                   "require_change_flag": 0
     *                               }
     *                           }
     *                       }
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */

    public function getPasswordReissue(Request $request)
    {
        $user_cd = $request->user_cd;
        $user_name = $request->user_name;
        $password_change = $request->password_change;
        $org_cd = $request->org_cd;
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $data = $this->service->getPasswordReissue($user_cd, $user_name, $password_change, $org_cd, $limit, $offset);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\POST(
     *      path="/api/password-reissue/send-mail-password-reissue",
     *      operationId="SendMailPasswordReissue",
     *      tags={"I02-S01"},
     *      summary="Password Reissue",
     *      description="Send Mail Password Reissue",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Send Mail Password Reissue",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="user cd",
     *                      default="10001"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                             "user_cd": "10052",
     *                             "user_name": "湯山 沙祐里",
     *                             "user_post_type_name": "AL",
     *                             "require_change_flag": 1,
     *                             "org_label": "北海道営業所第2A"
     *                       }
     *                   }
     *              )
     *        ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function sendMailPasswordReissue(SendMailPasswordReissuerRequest $request)
    {
        try {
            DB::beginTransaction();

            $userInfo = $this->service->getInfoSendEmail($request->user_cd);
            if(empty($userInfo['user'][0]->mail_address ?? '')){
                return $this->responseErrorValidate(__('password_reissue.mail_does_not_exist'));
            }

            // create new password
            $generate_pass = $this->generate_pass();
            // update password database

            if (!empty($generate_pass)) {
                $pass_word = bcrypt($generate_pass);
                $this->service->updatePasswordReset($request->user_cd, $pass_word);
            }

            $system_name = count((array)$userInfo['email']) > 0 ? $userInfo['email'][0]->parameter_value : '';
            $user_name = count((array)$userInfo['user']) > 0 ? $userInfo['user'][0]->user_name : '';
            $mail_to = count((array)$userInfo['mailto']) > 0 ? $userInfo['mailto'][0]->parameter_value : '';

            $data = [
                'title' => "【" . $system_name . "】パスワード再発行のお知らせ",
                'system_name' => $system_name,
                'new_password' => $generate_pass,
                'user_name' => $user_name,
                'mail_to' => $mail_to
            ];


            $emailTo = count((array)$userInfo['user']) > 0 ? $userInfo['user'][0]->mail_address : '';
            $mailable = new PasswordReissueMail($data);
            SendEmail::dispatchNow($emailTo, $mailable);

            // send mail user
            DB::commit();

            $data = $this->service->getInfoUserResult($request->user_cd);
            return $this->responseSuccess(__('password_reissue.success'), $data);
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('password_reissue.system_error'));
        }
    }

    public function checkForCharacterCondition($string)
    {
        return (bool) preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[a-zA-Z])[\x20-\x7E]{8,25}$/', $string);
    }

    public function generate_pass()
    {
        global $j;
        $allowedCharacters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*';
        $pass = '';
        $length = 10;
        $max = strlen($allowedCharacters) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pass .= $allowedCharacters[random_int(0, $max)];
        }

        if ($this->checkForCharacterCondition($pass)) {
            return $pass;
        } else {
            $j++;
            return $this->generate_pass();
        }
    }
}
