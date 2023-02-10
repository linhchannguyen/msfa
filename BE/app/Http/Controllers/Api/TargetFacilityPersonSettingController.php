<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TargetFacilityPersonSetting\SaveTargetFacilityPersonSettingRequest;
use App\Services\SystemParameterService;
use App\Services\TargetFacilityPersonSettingService;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\InFacilityTargetPersonManagementService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TargetFacilityPersonSettingOutput;
use App\Http\Requests\Api\TargetFacilityPersonSetting\ExportTargetFacilityPersonSetting;

class TargetFacilityPersonSettingController extends Controller
{
    private $service;

    public function __construct(TargetFacilityPersonSettingService $service, SystemParameterService $system)
    {
        $this->service = $service;
        $this->system = $system;
        $this->middleware('role.has:' . config('role.CALL_IMPLEMENTER.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\GET(
     *    path="/api/target-facility-person-setting/get-screen-data",
     *    operationId="getScreenDataTargetFacilityPersonSetting",
     *    tags={"G01-S03"},
     *    summary="Target Facility Person Setting",
     *    description="Get Screen Data",
     *    security={{"jwt": {}}},
     *    @OA\Response(response=200, description="Response successfully",
     *           @OA\JsonContent(
     *            type="object",
     *            example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                           "targetProduct": {
     *                               {
     *                                   "target_product_cd": "ABC",
     *                                   "target_product_name": "ターゲットABC",
     *                                   "start_date": "2019-04-01",
     *                                   "end_date": "9999-12-31"
     *                               },
     *                               {
     *                                   "target_product_cd": "DDD",
     *                                   "target_product_name": "ターゲットDDD",
     *                                   "start_date": "2019-04-01",
     *                                   "end_date": "9999-12-31"
     *                               }
     *                           },
     *                           "segmentType": {
     *                               {
     *                                   "segment_type": "10",
     *                                   "segment_name": "重要",
     *                                   "start_date": "2019-04-01",
     *                                   "end_date": "9999-12-31"
     *                               }
     *                           },
     *                           "facilityTypeGroup": {
     *                               {
     *                                   "facility_type_group_cd": "01",
     *                                   "facility_type_group_name": "病院等"
     *                               },
     *                               {
     *                                   "facility_type_group_cd": "10",
     *                                   "facility_type_group_name": "診療所"
     *                               }
     *                           },
     *                          "parameterAddStock": {
     *                                "definition_value": "30"
     *                           }
     *                       }
     *                   }
     *            )
     *      ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *  )
     */
    public function getScreenData()
    {
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        $data =  $this->service->getScreenData($date_system);
        return $this->responseSuccess('success', $data);
    }


    /**
     *  @OA\GET(
     *    path="/api/target-facility-person-setting",
     *    operationId="getTargetFacilityPersonSetting",
     *    tags={"G01-S03"},
     *    summary="Target Facility Person Setting",
     *    description="Get In Target Facility Person Setting",
     *    security={{"jwt": {}}},
     *    @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="facility_type_group_cd",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="person_name",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="target_product_cd",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="segment_type",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *    @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *    @OA\Response(response=200, description="Response successfully",
     *    @OA\JsonContent(
     *    type="object",
     *    example={
     *                "status": "200",
     *                "message": "success",
     *                "data": {
     *                    "records": {
     *                        {
     *                            "facility_cd": "011080005",
     *                            "facility_name": "国立大学法人村上大学医学部附属病院（札幌市厚別区）",
     *                            "person_cd": "01108159",
     *                            "person_name": "福岡 直幸",
     *                            "department_name": "糖尿病・内分泌・代謝内科",
     *                            "sub_target": {},
     *                            "display_position_name": "役職なし",
     *                            "segment_list": {
     *                                {
     *                                    "segment_type": "10",
     *                                    "segment_name": "重要"
     *                                },
     *                                {
     *                                    "segment_type": "20",
     *                                    "segment_name": "KOL"
     *                                }
     *                            },
     *                            "target_product_list": {
     *                                {
     *                                    "target_product_cd": "ABC"
     *                                }
     *                            }
     *                        }
     *                    },
     *                    "pagination": {
     *                        "total_items": 2221,
     *                        "total_pages": 23,
     *                        "previous_page": 1,
     *                        "next_page": 2,
     *                        "current_page": 1,
     *                        "first_page": 1,
     *                        "last_page": 23
     *                    }
     *                }
     *            }
     *        )
     *    ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *  )
     */
    public function getTargetFacilityPersonSetting(Request $request)
    {
        $user_cd = $request->user_cd;
        $facility_type_group_cd = $request->facility_type_group_cd;
        $person_name = $request->person_name;
        $target_product_cd = $request->target_product_cd;
        $segment_type = $request->segment_type;

        //sort
        $sort_order = $request->sort_order ?? [];

        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;

        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        $data = $this->service->getTargetFacilityPersonSetting($user_cd, $date_system, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type, $sort_order, $limit, $offset);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\POST(
     *    path="/api/target-facility-person-setting/save-target-facility-person-setting",
     *    operationId="saveTargetFacilityPersonSetting",
     *    tags={"G01-S03"},
     *    summary="Target Facility Person Setting",
     *    description="Save Target Facility Person Setting",
     *    security={{"jwt": {}}},
     *    @OA\RequestBody(
     *        description="Update User params",
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                type="object",
     *                @OA\Property(
     *                    property="records",
     *                    type="string",
     *                    description="records",
     *                    default="[{'facility_cd': '011010002','person_cd': '01101089','segment_list': [{'segment_type': '10'}],'target_product_list': []}]"
     *                )
     *            ),
     *        ),
     *    ),
     *    @OA\Response(response=201, description="Saved successfully",
     *           @OA\JsonContent(
     *            type="object",
     *            example={
     *                     "status": "201",
     *                     "message": "正常に保存しました。",
     *                     "data": {}
     *                 }
     *            )
     *      ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *  )
     */
    public function saveTargetFacilityPersonSetting(SaveTargetFacilityPersonSettingRequest $request)
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
            return $this->responseCreated(__('targetfacilitypersonsetting.save'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('targetfacilitypersonsetting.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *    path="/api/target-facility-person-setting/export",
     *    operationId="exportTargetFacilityPersonSetting",
     *    tags={"G01-P01"},
     *    summary="Target Facility Person Setting",
     *    description="Export Target Facility Person Setting",
     *    security={{"jwt": {}}},
     *    @OA\RequestBody(
     *        description="Export Target Facility Person Setting",
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                type="object",
     *                @OA\Property(
     *                    property="user_cd",
     *                    type="string",
     *                    description="user_cd",
     *                    default=""
     *                ),
     *                @OA\Property(
     *                    property="facility_type_group_cd",
     *                    type="string",
     *                    description="facility_type_group_cd",
     *                    default=""
     *                ),
     *                @OA\Property(
     *                    property="person_name",
     *                    type="string",
     *                    description="person_name",
     *                    default=""
     *                ),
     *                @OA\Property(
     *                    property="target_product_cd",
     *                    type="string",
     *                    description="target_product_cd",
     *                    default=""
     *                ),
     *                @OA\Property(
     *                    property="segment_type",
     *                    type="string",
     *                    description="segment_type",
     *                    default=""
     *                ),
     *            ),
     *        ),
     *    ),
     *    @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
     *                       "data": {}
     *                   }
     *              )
     *        ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *   )
     */
    public function exportTargetFacilityPersonSetting(Request $request)
    {
        try {
            $data_export = [];
            $segment_type = $request->segment_type;

            $user_cd = $request->user_cd;
            $facility_type_group_cd = $request->facility_type_group_cd;
            $person_name = $request->person_name;
            $target_product_cd = $request->target_product_cd;
            $segment_type = $request->segment_type;

            $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
            $date_system_print = date_create($this->system->getCurrentSystemDateTime())->format('Y/m/d H:i');
            $data_export['date_system_print'] = $date_system_print;

            $data_header =  $this->service->getScreenData($date_system);

            if (count((array)$data_header) > 0) {
                $data_export['targetProduct'] =  $data_header['targetProduct'] ?? [];
                $data_export['segmentType'] =  $data_header['segmentType'] ?? [];
            }

            //sort
            $sort_order = $request->sort_order ?? [];

            $data = $this->service->exportTargetFacilityPersonSetting($user_cd, $date_system, $facility_type_group_cd, $person_name, $target_product_cd, $segment_type, $sort_order);
            $data_export['datas'] = $data;
            $date_system = date_format(date_create($this->system->getCurrentSystemDateTime()), 'Ymd');
            $file_name = 'ターゲット施設個人一覧_' . $date_system . '.xlsx';
            return Excel::download(new TargetFacilityPersonSettingOutput($data_export), $file_name, \Maatwebsite\Excel\Excel::XLSX);
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('infacilitytargetpersonmanagement.system_error'));
        }
    }
}
