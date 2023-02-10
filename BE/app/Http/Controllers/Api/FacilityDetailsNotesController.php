<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FacilityDetailsNotes\DeleteConsiderationRequest;
use App\Http\Requests\Api\FacilityDetailsNotes\GetFacilityDetailsNotesRequest;
use App\Http\Requests\Api\FacilityDetailsNotes\SaveConsiderationConfirmRequest;
use App\Http\Requests\Api\FacilityDetailsNotes\SaveNewModeRequest;
use App\Http\Requests\Api\FacilityDetailsNotes\UpdateModeRequest;
use App\Services\FacilityDetailsNotesService;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\AuthService;
use App\Services\SystemParameterService;

class FacilityDetailsNotesController extends Controller
{
    private $service, $auth, $system;

    public function __construct(FacilityDetailsNotesService $service, AuthService $auth, SystemParameterService $system)
    {
        $this->service = $service;
        $this->auth = $auth;
        $this->system = $system;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     *  @OA\Get(
     *      path="/api/facility-details-notes/get-screen-data",
     *      operationId="getScreenDataFacilityDetailsNotes",
     *      tags={"H01-S04"},
     *      summary="Facility Details Notes",
     *      description="Get Screen Data",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *       type="object",
     *       example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       {
     *                   "consideration_type": "10",
     *                   "consideration_type_name": "訪問規制"
     *                       },
     *                       {
     *                   "consideration_type": "20",
     *                   "consideration_type_name": "薬審"
     *                       }
     *                   }
     *               }
     *          )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getScreenData()
    {
        $data = $this->service->getScreenData();
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\Get(
     *      path="/api/facility-details-notes",
     *      operationId="getFacilityDetailsNotes",
     *      tags={"H01-S04"},
     *      summary="Facility Details Notes",
     *      description="Get Facility Details Notes",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="content",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="end_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="consideration_type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *      type="object",
     *      example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       {
     *                           "consideration_type": "10",
     *                           "consideration_type_name": "訪問規制",
     *                           "create_org_label": "lable org",
     *                           "create_user_name": "abc",
     *                           "last_update_datetime": "2021/12/26",
     *                           "consideration_contents": "abc",
     *                           "facility_consideration_id": 10,
     *                           "announcement": {
     *                               {
     *                                   "user_cd": "10009",
     *                                   "confirmed_flag": 1,
     *                                   "org_cd": "11122",
     *                                   "org_label": "東北北営岩手A",
     *                                   "user_name": "小峯 嘉彦"
     *                               },
     *                               {
     *                                   "user_cd": "10010",
     *                                   "confirmed_flag": 1,
     *                                   "org_cd": "11123",
     *                                   "org_label": "東北北営秋田A",
     *                                   "user_name": "板井 さちえ"
     *                               }
     *                           },
     *                            "m_facility_user": {
     *                               {
     *                                   "user_cd": "10036"
     *                               }
     *                           }
     *                       }
     *                   }
     *               }
     *          )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getFacilityDetailsNotes(GetFacilityDetailsNotesRequest $request)
    {
        $facility_cd = $request->facility_cd;
        $content = $request->content;
        $start_date = !empty($request->start_date) ? date_create($request->start_date)->format('Y-m-d')  : null;
        $end_date = !empty($request->end_date) ? date_create($request->end_date)->format('Y-m-d')  : null;
        $consideration_type = $request->consideration_type;
        $data = $this->service->getFacilityDetailsNotes($facility_cd, $content, $start_date, $end_date, $consideration_type);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\POST(
     *      path="/api/facility-details-notes/save-consideration-confirm",
     *      operationId="saveConsiderationConfirm",
     *      tags={"H01-S04"},
     *      summary="Facility Details Notes",
     *      description="Save Consideration Confirm",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Save Consideration Confirm Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="facility_consideration_id",
     *                      type="number",
     *                      description="Facility Consideration Id",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="confirmed_flag",
     *                      type="number",
     *                      description="Confirmed Flag",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
     *                       "data": {}
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function saveConsiderationConfirm(SaveConsiderationConfirmRequest $request)
    {
        DB::beginTransaction();
        try {
            $facility_consideration_id = $request->facility_consideration_id;
            $confirmed_flag = $request->confirmed_flag;
            $user_cd =  $this->getCurrentUser();
            $confirmed_datetime = $this->system->getCurrentSystemDateTime();
            $user_cd =  $this->getCurrentUser();
            $this->service->saveConsiderationConfirm($facility_consideration_id, $confirmed_flag,$user_cd,$confirmed_datetime);
            DB::commit();
            return $this->responseCreated(__('facilitydetailsnotes.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('facilitydetailsnotes.system_error'));
        }
    }

    /**
     *  @OA\Delete(
     *      path="/api/facility-details-notes/delete-consideration",
     *      operationId="deleteConsideration",
     *      tags={"H01-S04"},
     *      summary="Facility Details Notes",
     *      description="Delete Consideration",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete User params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="facility_consideration_id",
     *                      type="number",
     *                      description="Facility Consideration Id",
     *                      default="10"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="Delete successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "204",
     *                       "message": "正常に削除しました。",
     *                       "data": {}
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function deleteConsideration(DeleteConsiderationRequest $request)
    {
        DB::beginTransaction();
        try {
            $facility_consideration_id = $request->facility_consideration_id;
            $this->service->deleteConsideration($facility_consideration_id);
            DB::commit();
            return $this->responseCreated(__('facilitydetailsnotes.delete'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('facilitydetailsnotes.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/facility-details-notes/save-new-mode",
     *      operationId="saveNewMode",
     *      tags={"H01-S04"},
     *      summary="Facility Details Notes",
     *      description="Save New Mode",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *      description="Save New Mode Params",
     *      required=true,
     *      @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="number",
     *                      description="Facility Cd",
     *                      default="011010002"
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_type",
     *                      type="number",
     *                      description="Consideration Type",
     *                      default="20"
     *                  ),
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="number",
     *                      description="User Cd",
     *                      default="[{user_cd : 10001} , {user_cd : 10002}]"
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_contents",
     *                      type="string",
     *                      description="Consideration Contents",
     *                      default="kkkkkkkkkkk"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      description="Facility Short Name",
     *                      default="田口医師会保健医療センター（札幌市中央区）"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
     *                       "data": {}
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function saveNewMode(SaveNewModeRequest $request)
    {
        DB::beginTransaction();
        try {
            $facility_cd = $request->facility_cd;
            $consideration_type = $request->consideration_type;
            $confirmation_request_destination = $request->confirmation_request_destination;
            $consideration_contents = $request->consideration_contents;
            $facility_short_name = $request->facility_short_name;

            //user login
            $user_cd_login =  $this->getCurrentUser();
            $data_user = $this->auth->getInfoUser($user_cd_login);

            $data_system = $this->system->getCurrentSystemDateTime();
            $this->service->saveNewMode($facility_cd, $consideration_type, $confirmation_request_destination, $consideration_contents, $data_system, $data_user, $facility_short_name);
            DB::commit();
            return $this->responseCreated(__('facilitydetailsnotes.save'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('facilitydetailsnotes.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/facility-details-notes/update-mode",
     *      operationId="updateMode",
     *      tags={"H01-S04"},
     *      summary="Facility Details Notes",
     *      description="Update Mode",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *      description="Update Mode Params",
     *      required=true,
     *      @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="number",
     *                      description="Facility Cd",
     *                      default="011010002"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_consideration_id",
     *                      type="number",
     *                      description="Facility Consideration Id",
     *                      default="12"
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_type",
     *                      type="number",
     *                      description="Consideration Type",
     *                      default="20"
     *                  ),
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="number",
     *                      description="User Cd",
     *                      default="[{user_cd : 10002}]"
     *                  ),
     *                  @OA\Property(
     *                      property="user_cd_delete",
     *                      type="number",
     *                      description="User Cd",
     *                      default="[{user_cd : 10002}]"
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_contents",
     *                      type="string",
     *                      description="Consideration Contents",
     *                      default="kkkkkkkkkkk"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      description="Facility Short Name",
     *                      default="田口医師会保健医療センター（札幌市中央区）"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
     *                       "data": {}
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function updateMode(UpdateModeRequest $request)
    {
        DB::beginTransaction();
        try {
            $facility_cd = $request->facility_cd;
            $facility_consideration_id = $request->facility_consideration_id;
            $consideration_type = $request->consideration_type;
            $confirmation_request_destination = $request->confirmation_request_destination;
            $consideration_contents = $request->consideration_contents;
            $confirmation_request_delete = $request->confirmation_request_delete;
            $facility_short_name = $request->facility_short_name;

            //user login
            $user_cd_login =  $this->getCurrentUser();
            $data_user_login = $this->auth->getInfoUser($user_cd_login);

            $data_system = $this->system->getCurrentSystemDateTime();
            $this->service->updateMode($facility_cd, $facility_consideration_id, $consideration_type, $confirmation_request_destination, $confirmation_request_delete, $consideration_contents, $data_user_login, $data_system, $facility_short_name);
            DB::commit();
            return $this->responseCreated(__('facilitydetailsnotes.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('facilitydetailsnotes.system_error'));
        }
    }
}
