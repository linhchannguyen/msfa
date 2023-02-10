<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Traits\DateTimeTrait;
use App\Http\Controllers\Controller;
use App\Services\PersonDetailNetworkService;
use App\Http\Requests\Api\PersonDetailNetwork\PersonDetailNetworkRequest;

class PersonDetailNetworkController extends Controller
{
    use DateTimeTrait;
    private $service;

    public function __construct(PersonDetailNetworkService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/person-detail-network/get-screen-data/{person_cd}",
     *      operationId="getScreenDataNetwork",
     *      tags={"H02-S07"},
     *      summary="Get Screen Data",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "personal_detail": {
     *                          {
     *                              "person_cd": "01101001",
     *                              "home_prefecture_cd": "3230220",
     *                              "graduation_university_cd": "348",
     *                              "prefecture_cd": null,
     *                              "prefecture_name": null,
     *                              "university_cd": null,
     *                              "university_name": null,
     *                              "graduation_year": "27",
     *                              "facility_cd": null,
     *                              "facility_name": null,
     *                              "medical_office_facility_cd": null,
     *                              "medical_office_department_cd": null,
     *                              "medical_office_name": null,
     *                              "department_name": null,
     *                              "_graduation_year": "大正27年卒"
     *                          }
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function getScreenDataNetwork($person_cd, PersonDetailNetworkRequest $request)
    {
        try{
            $personal_detail = $this->service->personalDetail($person_cd);
            $era_list = $this->service->getEra(JAPANESE_CALENDAR_ERA_CLASSIFICATION);
            foreach($personal_detail as &$value){
                $value->_graduation_year = "";
                $this->getEra($era_list, $value->graduation_year, $value->_graduation_year);
                $value->_graduation_year .= ($value->_graduation_year != "") ? GRADUATE_YEAR : "";
            }
            $data['personal_detail'] = $personal_detail;
            return $this->responseSuccess("success", $data);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/person-detail-network/search/{id}",
     *      operationId="searchPersonDetailNetwork",
     *      tags={"H02-S07"},
     *      summary="Search Person Detail Network",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Search Person Detail Network",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="facility cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="graduation_year",
     *                      type="string",
     *                      description="graduation year",
     *                      default="27"
     *                  ),
     *                  @OA\Property(
     *                      property="graduation_university_cd",
     *                      type="string",
     *                      description="graduation university cd",
     *                      default="324"
     *                  ),
     *                  @OA\Property(
     *                      property="home_prefecture_cd",
     *                      type="integer",
     *                      description="home prefecture cd",
     *                      default="2120120"
     *                  ),
     *                  @OA\Property(
     *                      property="medical_office_name",
     *                      type="integer",
     *                      description="medical office name",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="limit",
     *                      type="integer",
     *                      description="limit",
     *                      default="100"
     *                  ),
     *                  @OA\Property(
     *                      property="offset",
     *                      type="integer",
     *                      description="offset",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *          type="object",
     *          example={
     *                 "status":"200",
     *                 "data":{
     *                      "records": {
     *                          {
     *                              "person_cd": "01662002",
     *                              "person_name": "西本 敏哉",
     *                              "person_name_kana": "ニシモト トシヤ",
     *                              "home_prefecture_cd": "2120120",
     *                              "graduation_university_cd": "324",
     *                              "facility_cd": "016620002",
     *                              "department_cd": "9999",
     *                              "display_position_cd": "501",
     *                              "facility_short_name": "田口医師会保健医療センター（厚岸町）",
     *                              "facility_short_name_kana": "ﾀｸﾞﾁｲｼｶｲﾎｹﾝｲﾘﾖｳｾﾝﾀ-ｱｯｹｼﾁｮｳ",
     *                              "facility_name": "一般社団法人田口医師会保健医療センター（厚岸町）",
     *                              "medical_office_facility_cd": null,
     *                              "medical_office_name": null,
     *                              "medical_office_department_cd": null,
     *                              "department_name": "その他",
     *                              "prefecture_name": "北海道",
     *                              "university_name": null,
     *                              "graduation_year": "27",
     *                              "user_cd": "10162",
     *                              "user_name": "村元 麻衣香",
     *                              "org_cd": "11112",
     *                              "org_label": "北海道営業所第2A"
     *                          },
     *                      },
     *                      "pagination": {
     *                          {
     *                              "total_items": 1,
     *                              "total_pages": 1,
     *                              "previous_page": 1,
     *                              "next_page": 1,
     *                              "current_page": 1,
     *                              "first_page": 1,
     *                              "last_page": 1
     *                          }
     *                      }
     *                 }
     *             }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function searchPersonDetailNetwork(Request $request)
    {
        try{
            set_time_limit(0);
            ini_set('memory_limit', '2048M');
            $conditions['facility_cd'] = $request->facility_cd ?? "";
            $conditions['graduation_year'] = $request->graduation_year ?? "";
            $conditions['graduation_university_cd'] = $request->graduation_university_cd ?? "";
            $conditions['home_prefecture_cd'] = $request->home_prefecture_cd ?? "";
            $conditions['medical_office_name'] = $request->medical_office_name ?? "";
            $conditions['medical_office_facility_cd'] = $request->medical_office_facility_cd ?? "";
            $conditions['medical_office_department_cd'] = $request->medical_office_department_cd ?? "";
            if($conditions['facility_cd'] == "" && $conditions['graduation_year'] == ""
                && $conditions['graduation_university_cd'] == "" && $conditions['home_prefecture_cd'] == ""
                && $conditions['medical_office_name'] == "" && $conditions['medical_office_facility_cd'] == ""
                && $conditions['medical_office_department_cd'] == ""){
                $result['records'] = [];
                $result['pagination'] = [];
            }else{
                $limit = $request->filled('limit') ? $request->limit : 100;
                $offset = $request->filled('offset') ? $request->offset : 0;
                $data = $this->service->search($conditions, $limit, $offset);
                $result['records'] = empty($data['records']) ? [] : $data['records'];
                $result['pagination'] = $data['pagination'];
                $era_list = $this->service->getEra('元号');
                foreach($result['records'] as &$value){
                    $value->_graduation_year = "";
                    $this->getEra($era_list, $value->graduation_year, $value->_graduation_year);
                    $value->_graduation_year .= ($value->_graduation_year != "") ? GRADUATE_YEAR : "";
                }
            }
            return $this->responseSuccess('success', $result);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/person-detail-network/workplace-history/{person_cd}",
     *      operationId="getWorkplaceHistoryList",
     *      tags={"H02-S07"},
     *      summary="Workplace History List",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"200",
     *                     "data":{
     *                          "records": {
     *                              {
     *                                  "person_cd": "01101001",
     *                                  "facility_cd": "011010005",
     *                                  "facility_short_name": "村上大学医学部附属病院（札幌市中央区）",
     *                                  "department_cd": "1601",
     *                                  "department_name": "整形外科",
     *                                  "hospital_position_cd": "501",
     *                                  "hospital_position_name": "役職なし",
     *                                  "academic_position_cd": "",
     *                                  "academic_position_name": null,
     *                                  "start_date": "2011-04-01",
     *                                  "end_date": "2013-08-31"
     *                              }
     *                          },
     *                          "pagination": {
     *                              {
     *                                  "total_items": 1,
     *                                  "total_pages": 1,
     *                                  "previous_page": 1,
     *                                  "next_page": 1,
     *                                  "current_page": 1,
     *                                  "first_page": 1,
     *                                  "last_page": 1
     *
     *                              }
     *                          }
     *                     }
     *                 }
     *              )
     *      ),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function getWorkplaceHistoryList($person_cd, PersonDetailNetworkRequest $request)
    {
        try{
            $person_cd = $person_cd == "" ? [] : $person_cd;
            $data = [];
            if(!empty($person_cd)){
                $data = $this->service->getWorkplaceHistoryList($person_cd);
            }
            return $this->responseSuccess('success', $data);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
    }
}
