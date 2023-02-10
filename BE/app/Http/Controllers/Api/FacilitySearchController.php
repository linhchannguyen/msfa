<?php

namespace App\Http\Controllers\Api;

use App\Services\FacilitySearchService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FacilitySearch\FacilitySearchDeatilRequest;
use App\Http\Requests\Api\FacilitySearch\FacilitySearchConsultationTimeRequest;
use App\Http\Requests\Api\FacilitySearch\AllFacilitySearchRequest;
use App\Http\Requests\Api\FacilitySearch\SaveSegmentTypeFacilitySearchRequest;

use Exception;
use Illuminate\Support\Facades\DB;

class FacilitySearchController extends Controller
{
    private $service;

    public function __construct(FacilitySearchService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/facility-search",
     *      operationId="listFacilitySearch",
     *      tags={"H01-S01"},
     *      summary="Get list Facility Group",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_short_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="phone",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="prefecture_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="municipality_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="ultmarc_delete_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_consideration_options",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "facility_short_name": "秋元医院　医療（札幌市中央区）",
     *                      "facility_cd": "011010013",
     *                      "last_update_datetime": "",
     *                      "address": "札幌市中央区大東町１－９－１",
     *                      "phone": "06-6921-1994",
     *                      "user_cd": "10036",
     *                      "user_name": "金野 茉奈実",
     *                      "org_cd": "11110",
     *                      "org_label": "北海道営業所",
     *                      "confirmed_flag": ""
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getData(AllFacilitySearchRequest $request)
    {
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getData($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/facility-search/information",
     *      operationId="FacilityInformation",
     *      tags={"H01-S02"},
     *      summary="Get Facility Information",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "information": {
     *                              "facility_name": "国立大学法人村上大学医学部附属病院（札幌市中央区）",
     *                              "address": "札幌市中央区山田丘２－１５",
     *                              "phone": "06-6879-5111",
     *                              "prefecture_cd": "01",
     *                              "prefecture_name": "北海道",
     *                              "municipality_cd": "101",
     *                              "municipality_name": "札幌市中央区",
     *                              "management_body_cd": "180",
     *                              "management_body_name": "国大法",
     *                              "infirmary_type": "1",
     *                              "infirmary_type_name": "一般",
     *                              "total_bed_count": "1086",
     *                              "general_bed_count": "1034",
     *                              "mental_bed_count": "52",
     *                              "infection_bed_count": "0",
     *                              "other_bed_count": "0",
     *                              "consultation_hours_note": null
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getFacilityInformation(FacilitySearchDeatilRequest $request)
    {
        $data = $this->service->getFacilityInformation($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/facility-search/basic-information",
     *      operationId="FacilityBasicInformation",
     *      tags={"H01-S02"},
     *      summary="Get Facility Basic Information",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "detail": {
     *                          "facility_cd": "011030013",
     *                          "facility_name": "医療法人秋元医院（札幌市東区）",
     *                          "address": "札幌市東区大東町１－９－１",
     *                          "phone": "06-6921-1994",
     *                          "prefecture_cd": "01",
     *                          "prefecture_name": "北海道",
     *                          "municipality_cd": "103",
     *                          "municipality_name": "札幌市東区",
     *                          "management_body_cd": "431",
     *                          "management_body_name": "医療",
     *                          "infirmary_type": "7",
     *                          "infirmary_type_name": "診療所",
     *                          "total_bed_count": "0",
     *                          "general_bed_count": "0",
     *                          "mental_bed_count": "0",
     *                          "infection_bed_count": "0",
     *                          "other_bed_count": "0",
     *                          "tuberculous_bed_count": "0",
     *                          "consultation_hours_note": null
     *                      },
     *                      "staff_history": {
     *                          {
     *                              "main_flg": 1,
     *                              "start_date": "2019-04-01",
     *                              "end_date": "9999-12-31",
     *                              "user_cd": "10036",
     *                              "profile_image_file_id": 33,
     *                              "user_name": "金野 茉奈実",
     *                              "file_url": "https://bu5-msfa.s3.ap-northeast-1.amazonaws.com/profile/09cb005162974f949ca561eb1f15cddd.jpg"
     *                          }
     *                      },
     *                      "medical_subjects": {
     *                          {
     *                              "medical_subjects_cd": "A01",
     *                              "medical_subjects_name": "一般内科"
     *                          },
     *                          {
     *                              "medical_subjects_cd": "E02",
     *                              "medical_subjects_name": "放射線科"
     *                          },
     *                          {
     *                              "medical_subjects_cd": "L01",
     *                              "medical_subjects_name": "小児科"
     *                          }
     *                      },
     *                      "related": {
     *                          {
     *                              "relation_facility_cd": "011030105",
     *                              "facility_short_name": "秋元医院　医療（札幌市東区）",
     *                              "relation_type": "20",
     *                              "definition_label": "処方施設"
     *                          }
     *                      },
     *                      "parent": {
     *                          {
     *                              "relation_facility_cd": "011030105",
     *                              "facility_short_name": "秋元医院　医療（札幌市東区）",
     *                              "relation_type": "20",
     *                              "definition_label": "処方施設"
     *                          }
     *                      },
     *                      "consultation_time": {
     *                              "consultation_hours_note": ""
     *                      },
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getFacilityDetail(FacilitySearchDeatilRequest $request)
    {
        $data = $this->service->getFacilityDetail($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\PUT(
     *      path="/api/facility-search/basic-information/consultation-time",
     *      operationId="saveConsultationTimeFacility",
     *      tags={"H01-S02"},
     *      summary="Save Consultation Time Facility",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="consultation_hours_note",in="query", @OA\Schema(type="string")),
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
    public function saveConsultationTimeFacility(FacilitySearchConsultationTimeRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->user_cd = $this->getCurrentUser();
            $this->service->saveConsultationTimeFacility($request);
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
     *      path="/api/facility-search/working-individual/index",
     *      operationId="getIndexWorkingIndividualFacility",
     *      tags={"H01-S03"},
     *      summary="get Index Working Individual Facility",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
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
    public function getIndexWorkingIndividualFacility()
    {
        $data = $this->service->getIndexWorkingIndividualFacility();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/facility-search/working-individual",
     *      operationId="WorkingIndividualFacility",
     *      tags={"H01-S03"},
     *      summary="Get Working Individual Facility",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_list",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_list.facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_list.person_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_list.part_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_list.segment_list",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_list.segment_list.segment_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_list.segment_list.checked",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_list.segment_list.delete_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
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
    public function getWorkingIndividualFacility(FacilitySearchDeatilRequest $request)
    {
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $data = $this->service->getWorkingIndividualFacility($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\PUT(
     *      path="/api/facility-search/working-individual/segment-type",
     *      operationId="SaveSegmentTypeFacility",
     *      tags={"H01-S03"},
     *      summary="Save Segment Type Facility",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_search",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="person_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_segment",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="segment_type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "facility_cd": "011010002",
     *                      "person_cd": "01101006",
     *                      "person_name": "桜井 兼典",
     *                      "department_cd": "9999",
     *                      "department_name": "その他",
     *                      "hospital_position_cd": "501",
     *                      "academic_position_cd": "",
     *                      "position_name": "役職なし",
     *                      "segment_type": "20",
     *                      "segment_name": "KOL",
     *                      "part_type": null,
     *                      "definition_label": null
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function saveSegmentTypeFacility(SaveSegmentTypeFacilitySearchRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->service->saveSegmentTypeFacility($request);
            DB::commit();
            return $this->responseCreated('正常に更新しました。');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }
}
