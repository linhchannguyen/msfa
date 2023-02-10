<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\FacilityPersonalService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Facility\FacilityPersonalRequest;
use App\Http\Requests\Api\ConditionArea\ConditionAreaRequest;

class FacilityPersonalController extends Controller
{
    private $service;

    public function __construct(FacilityPersonalService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/list-facility-personal",
     *      operationId="listFacilityPersonal",
     *      tags={"Z05-S04"},
     *      summary="Get Facility Personal",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="segment_type",in="query", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      {
     *                          "facility_cd": "10",
     *                          "facility_short_name": "コープ淀川クリニック（札幌市中央区）",
     *                          "department_cd": "9999",
     *                          "department_name": "その他",
     *                          "person_cd": "01101059",
     *                          "person_name": "岡崎 栄之介",
     *                          "position_cd": "062",
     *                          "position_name": "院長"
     *                       }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getData(FacilityPersonalRequest $request)
    {
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $result = $this->service->getData($request);
        return $this->responseSuccess('success', $result);
    }

    /**
     * @OA\GET(
     *      path="/api/list-facility-personal/facility",
     *      operationId="listFacilityPersonalFacility",
     *      tags={"Z05-S04"},
     *      summary="Get list Facility Personal Facility",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="select_group_id",in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="target_product_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="prefecture_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="municipality_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_category_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                     {
     *                         "facility_cd": "011010001",
     *                         "facility_short_name_kana": "ｼﾘﾂｱｻﾀﾞｼﾐﾝﾋﾞﾖｳｲﾝｻｯﾎﾟﾛｼﾁｭｳｵｳｸ"
     *                     }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getListFacility(ConditionAreaRequest $request)
    {
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $request->login_user_cd = $this->getCurrentUser();
        if (!$request->facility_cd && !$request->select_group_id && !$request->target_product_cd && !$request->prefecture_cd && !$request->municipality_cd && !$request->facility_category_type && !$request->facility_name && !$request->user_cd) {
            return $this->responseErrorValidate('検索条件は1つ以上指定する必要があります。');
        }
        $data = $this->service->getListFacility($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\GET(
     *      path="/api/list-facility-personal/medical-staff",
     *      operationId="listFacilityPersonalMedicalStaff",
     *      tags={"Z05-S04"},
     *      summary="Change Product",
     *      description="GEt Facility Personal Medical Staff",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      {
     *                          "medical_staff_cd": "C010",
     *                          "medical_staff_name": "研修医",
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getMedicalStaff()
    {

        $data = $this->service->allMedicalStaff();
        return $this->responseCreated('success', $data);
    }
}
