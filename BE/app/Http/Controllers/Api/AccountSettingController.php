<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\AccountSettingService;
use App\Services\OrganizationService;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AccountSetting\EditAccountInfoRequest;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\AccountSetting\AccountInfoRequest;
use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;
use App\Traits\Base64StringFileTrait;

class AccountSettingController extends Controller
{
    private $service;
    private $fileService;
    private $orgService, $convention;
    use Base64StringFileTrait;

    public function __construct(
        AccountSettingService $service,
        FileService $fileService,
        OrganizationService $orgService,
        ConventionSearchRepositoryInterface $convention
    ) {
        $this->service = $service;
        $this->convention = $convention;
        $this->fileService = $fileService;
        $this->orgService = $orgService;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/account/info",
     *      operationId="getAccountInfo",
     *      tags={"Z04-S01"},
     *      summary="Get account info",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "200",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function getAccountInfo(AccountInfoRequest $request)
    {
        $data = $this->service->getAccountInfo($request->user_cd);
        $avatar_file_url = $data->avatar_file_url ?? "";
        $data->avatar_image_data = $this->encodeBase64String($avatar_file_url);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/account/point",
     *      operationId="getAccountPoint",
     *      tags={"Z04-S01"},
     *      summary="Get account point",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "200",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function getAccountPoint(Request $request)
    {
        $user_cd = !empty($request->user_cd) ? $request->user_cd : $this->getCurrentUser();
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;

        if ($request->filled('point_target_type')) {
            $data = $this->service->getAccountPoint($user_cd, $request->point_target_type, $limit, $offset);
        } else {
            $data = $this->service->getAccountPoint($user_cd, '', $limit, $offset);
        }

        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\PUT(
     *      path="/api/account/info",
     *      operationId="putAccountInfo",
     *      tags={"Z04-S01"},
     *      summary="Change account info",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=201, description="Successful Response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Error"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function putAccountInfo(EditAccountInfoRequest $request)
    {
        DB::beginTransaction();
        try {
            $userCd = $this->getCurrentUser();
            $user_profile = $this->service->findAccount($userCd);

            $data = [
                'profile_image_file_id' => $user_profile->profile_image_file_id ?? '',
                'note_1' => $request->note_1 ?? '',
                'note_2' => $request->note_2 ?? '',
                'note_3' => $request->note_3 ?? '',
                'note_4' => $request->note_4 ?? '',
                'note_5' => $request->note_5 ?? ''
            ];

            $this->service->changeAccountInfo($userCd, $data);

            DB::commit();
            return $this->responseCreated(__('account_setting.save_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('account_setting.database_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/account/point_target_type",
     *      operationId="getPointTargetType",
     *      tags={"Z04-S01"},
     *      summary="Get point target types",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "200",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function getPointTargetType()
    {
        $data = $this->service->getPointTargetType();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\POST(
     *      path="/api/account/avatar",
     *      operationId="uploadAvatar",
     *      tags={"Z04-S01"},
     *      summary="Change account avatar",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\MediaType(mediaType="multipart/form-data"),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="avatar",
     *                      type="string",
     *                      format="binary"
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(response=201, description="Successful Response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function uploadAvatar(Request $request)
    {
        DB::beginTransaction();
        try {
            $userCd = $this->getCurrentUser();
            Storage::disk('public')->delete($request->avatar_file_name);
            $user_profile = $this->service->findAccount($userCd);
            $this->convention->deleteFile($request->file_id);
            $file = $request->file('avatar');
            $fileData = $this->fileService->uploadAndSaveProfileImage($file);
            $fileData->file_url = $this->encodeBase64String($fileData->file_url);
            if (!$fileData) {
                return $this->responseSystemError(__('account_setting.upload_error'));
            }
            $data = [
                'profile_image_file_id' => $fileData->file_id,
                'note_1' => $user_profile->note_1 ?? '',
                'note_2' => $user_profile->note_2 ?? '',
                'note_3' => $user_profile->note_3 ?? '',
                'note_4' => $user_profile->note_4 ?? '',
                'note_5' => $user_profile->note_5 ?? ''
            ];
            $this->service->updateAvatar($userCd, $fileData->file_id);
            $this->service->changeAccountInfo($userCd, $data);

            DB::commit();
            $message = __('account_setting.upload_success');
            return $this->responseCreated($message, $fileData);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('account_setting.database_error'));
        }
    }
}
