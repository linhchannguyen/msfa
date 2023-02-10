<?php

namespace App\Http\Controllers\Api;

use App\Exports\InFacilityTargetPersonManagementOutput;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InFacilityTargetPersonManagement\GetInFacilityTargetPersonManagementRequest;
use App\Http\Requests\Api\InFacilityTargetPersonManagement\GetScreenDataRequest;
use App\Http\Requests\Api\InFacilityTargetPersonManagement\SaveInFacilityTargetPersonRequest;
use App\Services\InFacilityTargetPersonManagementService;
use App\Services\SystemParameterService;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InFacilityTargetPersonManagementController extends Controller
{
    private $service;

    public function __construct(InFacilityTargetPersonManagementService $service, SystemParameterService $system)
    {
        $this->service = $service;
        $this->system = $system;
        $this->middleware('role.has:' . config('role.CALL_IMPLEMENTER.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\GET(
     *    path="/api/in-facility-target-person-management/get-screen-data",
     *    operationId="getScreenDataInFacilityTargetPersonManagement",
     *    tags={"G01-S02"},
     *    summary="In Facility Target Person Management",
     *    description="Get Screen Data",
     *    security={{"jwt": {}}},
     *    @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *    @OA\Response(response=200, description="Response successfully",
     *           @OA\JsonContent(
     *            type="object",
     *            example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       "targetProduct_a3": {
     *                           {
     *                               "target_product_cd": "ABC",
     *                               "target_product_name": "ターゲットABC",
     *                               "start_date": "2019-04-01",
     *                               "end_date": "9999-12-31"
     *                           },
     *                           {
     *                               "target_product_cd": "DDD",
     *                               "target_product_name": "ターゲットDDD",
     *                               "start_date": "2019-04-01",
     *                               "end_date": "9999-12-31"
     *                           }
     *                       },
     *                       "segmentType_c7": {
     *                           {
     *                               "segment_type": "10",
     *                               "segment_name": "重要",
     *                               "start_date": "2019-04-01",
     *                               "end_date": "9999-12-31"
     *                           }
     *                       },
     *                        "department_c2": {
     *                              {
     *                                  "department_cd": "0648",
     *                                  "department_name": "新生児・未熟児科"
     *                              }
     *                        },
     *                        "subTarget": {
     *                              {
     *                                  "target_product_cd": "ABC",
     *                                  "sub_target": "TEST"
     *                              }
     *                        },
     *                        "facilityInfo": {
     *                              "facility_cd": "011010004",
     *                              "facility_name": "河野病院（札幌市中央区）",
     *                              "facility_short_name": "河野病院（札幌市中央区）"
     *                        },
     *                          "parameterAddStock": {
     *                                "definition_value": "30"
     *                       }
     *                   }
     *               }
     *            )
     *      ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *  )
     */
    public function getScreenData(GetScreenDataRequest $request)
    {
        $facility_cd = $request->facility_cd;
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        $data =  $this->service->getScreenData($date_system, $facility_cd);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\GET(
     *    path="/api/in-facility-target-person-management",
     *    operationId="getInFacilityTargetPersonManagement",
     *    tags={"G01-S02"},
     *    summary="In Facility Target Person Management",
     *    description="Get In Facility Target Person Management",
     *    security={{"jwt": {}}},
     *    @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="department_cd",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="person_name",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="target_product_cd",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="segment_type",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="order_value",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="order_type",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *    @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *    @OA\Response(response=200, description="Response successfully",
     *    @OA\JsonContent(
     *    type="object",
     *    example={
     *               "status": "200",
     *               "message": "success",
     *               "data": {
     *                   "records": {
     *                       {
     *                           "facility_cd": "011010002",
     *                           "facility_name": "一般社団法人田口医師会保健医療センター（札幌市中央区）",
     *                           "person_cd": "01101002",
     *                           "person_name": "和田 晴生",
     *                           "department_name": "その他",
     *                           "position_name": "役職なし",
     *                           "segment_list": {
     *                               {
     *                                   "segment_type": "10",
     *                                   "segment_name": "重要"
     *                               }
     *                           },
     *                           "target_product_list": {
     *                               {
     *                                   "target_product_cd": "ABC"
     *                               }
     *                           }
     *                       }
     *                   },
     *                   "pagination": {
     *                       "total_items": 6,
     *                       "total_pages": 1,
     *                       "previous_page": 1,
     *                       "next_page": 1,
     *                       "current_page": 1,
     *                       "first_page": 1,
     *                       "last_page": 1
     *                   }
     *               }
     *           }
     *        )
     *    ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *  )
     */

    public function getInFacilityTargetPersonManagement(GetInFacilityTargetPersonManagementRequest $request)
    {
        $department_cd = $request->department_cd;
        $person_name = $request->person_name;
        $target_product_cd = $request->target_product_cd;
        $segment_type = $request->segment_type;
        $facility_cd = $request->facility_cd;
        $sort_order = $request->sort_order ?? [];
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        $data = $this->service->getInFacilityTargetPersonManagement($facility_cd, $date_system, $department_cd, $person_name, $target_product_cd, $segment_type, $sort_order, $limit, $offset);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\POST(
     *      path="/api/in-facility-target-person-management/save-in-facility-target-person",
     *      operationId="saveInFacilityTargetPerson",
     *      tags={"G01-S02"},
     *      summary="In Facility Target Person Management",
     *      description="Save In Facility Target Person Management",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Update User params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="records",
     *                      type="string",
     *                      description="records",
     *                      default="[{'facility_cd': '011010002','person_cd': '01101089','segment_list': [{'segment_type': '10'}],'target_product_list': []}]"
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
    public function saveInFacilityTargetPerson(SaveInFacilityTargetPersonRequest $request)
    {
        DB::beginTransaction();
        try {
            $records = $request->records;
            if (count($records) > 0) {
                foreach ($records as $dataItem) {
                    $dataItem = (object)$dataItem;
                    $this->service->saveSegmentList($dataItem->facility_cd, $dataItem->person_cd, $dataItem->segment_list);
                    $this->service->saveTargetProductList($dataItem->facility_cd, $dataItem->person_cd, $dataItem->target_product_list);
                }
            }
            DB::commit();
            return $this->responseCreated(__('infacilitytargetpersonmanagement.save'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('infacilitytargetpersonmanagement.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/in-facility-target-person-management/export",
     *      operationId="exportInFacility",
     *      tags={"G01-P01"},
     *      summary="In Facility Target Person Management",
     *      description="Export In Facility",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Export In Facility params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="department_cd",
     *                      type="string",
     *                      description="department_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      description="person_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="target_product_cd",
     *                      type="string",
     *                      description="target_product_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="segment_type",
     *                      type="string",
     *                      description="segment_type",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="facility_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="sort_order",
     *                      type="string",
     *                      description="sort_order",
     *                      default="[{'order_type' : 'target_product' , 'order_value' : 'ABC'}]"
     *                  ),
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
    public function exportInFacility(GetInFacilityTargetPersonManagementRequest $request)
    {
        try {
            $data_export = [];
            $department_cd = $request->department_cd;
            $person_name = $request->person_name;
            $target_product_cd = $request->target_product_cd;
            $segment_type = $request->segment_type;
            $facility_cd = $request->facility_cd;

            //sort
            $sort_order = $request->sort_order ?? [];

            $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
            $date_system_print = date_create($this->system->getCurrentSystemDateTime())->format('Y/m/d H:i');
            $data_export['date_system_print'] = $date_system_print;

            $data_header =  $this->service->getScreenData($date_system, $facility_cd);

            if (count((array)$data_header) > 0) {
                $data_export['targetProduct'] =  $data_header['targetProduct_a3'] ?? [];
                $data_export['segmentType'] =  $data_header['segmentType_c7'] ?? [];
            }
            $datas = $this->service->exportInFacilityTargetPersonManagement($facility_cd, $date_system, $department_cd, $person_name, $target_product_cd, $segment_type, $sort_order);

            $data_export['datas'] = $datas;

            $date_system = date_format(date_create($this->system->getCurrentSystemDateTime()), 'Ymd');
            $file_name = 'ターゲット施設個人一覧_' . $date_system . '.xlsx';

            return Excel::download(new InFacilityTargetPersonManagementOutput($data_export), $file_name, \Maatwebsite\Excel\Excel::XLSX);
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('infacilitytargetpersonmanagement.system_error'));
        }
    }
}
