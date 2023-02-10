<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\PostedUserManagementService;
use App\Http\Requests\Api\PostedUserManagement\ValidateUserRequest;
use App\Traits\Base64StringFileTrait;

class PostedUserManagementController extends Controller
{
    use Base64StringFileTrait;
    private $service;

    public function __construct(PostedUserManagementService $service)
    {
        $this->service = $service;
        $this->middleware('role.has:'.config('role.QA_MG.CODE').','.config('role.SYS_MG.CODE'));
    }
    /**
     * @OA\GET(
     *      path="/api/posting-prohibited-user-management/get-posting-prohibited",
     *      operationId="allPostingProhibited",
     *      tags={"E01-S04"},
     *      summary="Get all posting prohibited",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *          type="object",
     *          example={
     *                 "status":"success",
     *                 "data":{{
     *                     "user_cd": "10003",
     *                     "org_label": "営業部門",
     *                     "user_name": "吉良 光浩",
     *                     "avatar_image_data": "https://bu5-msfa.s3.ap-northeast-1.amazonaws.com/profile/786834523e364187b1aa6e326e80b0dd.jpg",
     *                     "avatar_image_type": "image/jpeg",
     *                     "prohibited_datetime": "2022-02-06 09:15:44"
     *                 }}
     *             }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function allPostingProhibited()
    {
        try{
            $data = $this->service->allPostingProhibited();
            foreach($data as $value){
                $value->avatar_image_data = $this->encodeBase64String($value->avatar_image_data);
            }
            return $this->responseSuccess('success', $data);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('qa.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/posting-prohibited-user-management/get-unsuitable-report",
     *      operationId="getUnsuitableReport",
     *      tags={"E01-S04"},
     *      summary="Get unsuitable report by user",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      default="10003"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *          type="object",
     *          example={
     *                 "status":"success",
     *                 "data":{{
     *                     "key_type": "10",
     *                     "key_id": 1,
     *                     "created_at": "2022-01-31 10:36:09",
     *                     "question_id": 1,
     *                     "contents": "10221",
     *                     "answer_note": null
     *                 }}
     *             }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getUnsuitableReport(ValidateUserRequest $request)
    {
        try{
            $data = $this->service->getUnsuitableReport($request->user_cd);
            return $this->responseSuccess('success', $data);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('qa.system_error'));
        }
    }

    /**
     * @OA\DELETE(
     *      path="/api/posting-prohibited-user-management/cancel-posting-prohibited",
     *      operationId="cancelPostingProhibited",
     *      tags={"E01-S04"},
     *      summary="Cancel posting prohibited",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      default="10003"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function cancelPostingProhibited(ValidateUserRequest $request)
    {
        DB::beginTransaction();
        try{
            $this->service->cancelPostingProhibited($request->user_cd);
            DB::commit();
            return $this->responseSuccess(__('qa.delete_successfully'));
        }catch(Exception $ex){
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('qa.system_error'));
        }
    }
}
