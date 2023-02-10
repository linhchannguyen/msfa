<?php

namespace App\Http\Controllers\Api;

use App\Services\DevelopService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Develop\DevelopLoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\AuthService;
use App\Services\RolePolicyService;
use App\Traits\Base64StringFileTrait;

class DevelopController extends Controller
{
    use Base64StringFileTrait;
    private $auth, $role_user;

    public function __construct(DevelopService $auth, RolePolicyService $role_user)
    {
        $this->role_user = $role_user;
        $this->auth = $auth;
    }

    /**
     * @OA\Post(
     *      path="api/develop/login",
     *      tags={"Z01_S02"},
     *      summary="Login Develop",
     *      description="Login Develop System",
     *      @OA\RequestBody(
     *          description="Develop login params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="developer_cd",
     *                      type="string",
     *                      description="Developer Cd",
     *                      default="dev01"
     *                  ),
     *                  @OA\Property(
     *                      property="password_hash",
     *                      type="string",
     *                      description="Password Hash",
     *                      default="12345678"
     *                  ),
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="User Cd",
     *                      default="tutx"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                         "status": "201",
     *                          "message": "正常にサインインしました。",
     *                          "data": {
     *                                      "developer_cd": "dev001",
     *                                      "developer_name": "dev",
     *                                      "user_cd": "10001",
     *                                      "user_name": "西嶋 洋明",
     *                                      "mail_address": "10001@dummy.ne.jp",
     *                                      "require_change_flag": false,
     *                                      "role": "R01",
     *                                      "role_name": "システム利用者",
     *                                      "avatar_image_data": "",
     *                                      "avatar_image_type": "",
     *                                      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOlwvXC8xMjcuMC4wLjE6ODAwMFwvYXBpXC9kZXZlbG9wXC9sb2dpbiIsImlhdCI6MTYzNjUxMzI5MSwiZXhwIjoxNjM5MTA1MjkxLCJuYmYiOjE2MzY1MTMyOTEsImp0aSI6IlRmc0YxUWRvazlXQk5XQTgiLCJzdWIiOiIxMDAwMSIsInBydiI6ImY3NTZlYzRhMWMwYjMzOGE5NGYxZWNhNGVkOWI1ODhmM2MxMDczYjcifQ.uqOYMjkYtoHtXcD7c2fe63fs4nbAw23qYONzA-rPzKQ"
     *                       }
     *                 }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function login(DevelopLoginRequest $request)
    {
        try {
            $credentials = $request->only('developer_cd','password_hash','user_cd');
            $credentials['is_user'] = false;
            $credentials['is_develop'] = true;
            $credentials['user_cd'] = $request->developer_cd;
            $user_login = 'is_develop';
            $customClaims = ['sub' => $request->user_cd];
            $info_develop = $this->auth->login($request->developer_cd);
            $develop_cd = $info_develop->user_cd ?? '';

            // get infor user login
            $data_user_temp =  (new AuthService)->getInfoUserLogin($request->user_cd);

            // validate record >= 2
            if (count((array)$data_user_temp) >= 2) {
                return $this->responseSystemError(__('auth.get_infos'));
            }else if (empty($data_user_temp[0]->org_cd ?? '')){
                return $this->responseErrorValidate(__('auth.user_org_not_exists'));
            }

            // get infor user cd
            $data_user = (new AuthService)->getInfoUser($request->user_cd);

            if(empty($develop_cd) || (!password_verify($request->password_hash ?? '' ,$info_develop->password ?? ''))){
                $data['developer_cd'] = [__('develop.invalid_token')];
                $data['password_hash'] = [ __('develop.invalid_token') ];
                return $this->responseUnauthorized(__('develop.invalid_token') , $data);
            }

            if(empty($data_user->user_cd ?? '')){
                $data['user_cd'] = [__('develop.check_user_exist')];
                return $this->responseErrorValidate(__('develop.check_user_exist'), $data);
            }

            if (! $token = JWTAuth::claims($customClaims)->attempt($credentials)) {
                return $this->responseErrorValidate(__('develop.invalid_token'));
            }

            $role_user = $this->role_user->getRoleList($request->user_cd);
            if(count($role_user) == 0){
                return $this->responseErrorValidate(__('auth.role_login'));
            }

            // get infor developer cd
            $user =  $this->getInfoCurrentUser($request->developer_cd, $user_login);

            $data = $user ? [
                'developer_cd' => $user->user_cd,
                'developer_name' => $user->developer_name,
                'user_cd' => $data_user->user_cd ?? '',
                'user_name' => $data_user->user_name ?? '',
                'mail_address' => $data_user->mail_address ?? '',
                'require_change_flag' => false,
                'avatar_image_data' => $this->encodeBase64String($data_user->avatar_image_data),
                'avatar_image_type' => $data_user->avatar_image_type ?? '',
                'org_cd' => $data_user->org_cd,
                'org_short_name' => $data_user->org_short_name,
                'org_label' => $data_user->org_label
            ] : null;

            $data['access_token'] = $token;

            return $this->responseCreated(__('develop.login_success'), $data);
        } catch (JWTException $e) {
            throw $e;
            return $this->responseSystemError(__('develop.system_error'));
        }
    }
}
