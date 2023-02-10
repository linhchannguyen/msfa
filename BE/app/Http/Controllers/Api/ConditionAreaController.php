<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\ConditionAreaService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ConditionArea\ConditionAreaRequest;
use App\Http\Requests\Api\ConditionArea\ConditionAreaRelationRequest;

class ConditionAreaController extends Controller
{
    private $service;

    public function __construct(ConditionAreaService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/list-condition-area",
     *      operationId="listconditionarea03",
     *      tags={"Z05-S03"},
     *      summary="Get list condition area S03",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "select_group": {
     *                          {
     *                              "definition_value": "10",
     *                              "definition_label": "施設"
     *                          }
     *                      },
     *                      "target_product": {
     *                           {
     *                               "target_product_cd": "ABC",
     *                               "target_product_name": "ターゲットABC"
     *                           }
     *                      },
     *                      "facility_category":{
     *                          {
     *                              "facility_category_name": "大学病院",
     *                              "facility_category_type": "01"
     *                          }
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getListItem(Request $request)
    {
        $request->user_cd = $this->getCurrentUser();
        $data = $this->service->getListItem($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/list-condition-area/facility",
     *      operationId="listconditionarea03facility",
     *      tags={"Z05-S03"},
     *      summary="Get list condition area facility S03 ",
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
}
