<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\FacilityGroupService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FacilityGroup\DeleteFacilityGroupRequest;
use App\Http\Requests\Api\FacilityGroup\UpdateFacilityGroupRequest;
use App\Http\Requests\Api\FacilityGroup\ChangeSelectFacilityGroupRequest;
use App\Http\Requests\Api\FacilityGroup\SelectFacilityGroupRequest;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FacilityGroupController extends Controller
{
    private $service;

    public function __construct(FacilityGroupService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/list-facility-group",
     *      operationId="listFacilityGroup",
     *      tags={"Z03-S01"},
     *      summary="Get list Facility Group",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "select_facility_group_id": 1,
     *                      "select_facility_group_name": "test",
     *                      "sort_order": 1,
     *                      "children": {
     *                          "facility_cd": "011010001",
     *                          "facility_short_name_kana": "ｱｻﾀﾞｼﾐﾝﾋﾞﾖｳｲﾝｻｯﾎﾟﾛｼﾁｭｳｵｳｸ"
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getData(SelectFacilityGroupRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getData($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/list-facility-group/delete",
     *      operationId="deleteFacilityGroup",
     *      tags={"Z03-S01"},
     *      summary="DeleteFacility Group",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="select_facility_group_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=301, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function deleteFacilityGroup(DeleteFacilityGroupRequest $request)
    {
        DB::beginTransaction();
        try {
            $select_facility_group_id = $request->select_facility_group_id;
            $this->service->deleteFacilityGroup($select_facility_group_id);
            DB::commit();
            return $this->responseNoContent(__('success'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/list-facility-group/update",
     *      operationId="updateFacilityGroup",
     *      tags={"Z03-S01"},
     *      summary="DeleteFacility Group",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="select_facility_group_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="select_facility_group_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="sort_order",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_facility",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=201, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function updateFacilityGroup(UpdateFacilityGroupRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->user_cd = $this->getCurrentUser();
            $this->service->updateFacilityGroup($request);
            DB::commit();
            return $this->responseCreated('success');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/list-facility-group/change-select",
     *      operationId="ChangeSelectFacilityGroup",
     *      tags={"Z03-S01"},
     *      summary="Change Select Facility Group",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility.*.select_facility_group_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility.*.select_facility_group_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility.*.sort_order",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=201, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function changeSelectFacilityGroup(ChangeSelectFacilityGroupRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->service->changeSelectFacilityGroup($request);
            DB::commit();
            return $this->responseCreated('success');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }
}
