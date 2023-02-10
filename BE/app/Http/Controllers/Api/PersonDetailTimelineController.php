<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Http\Controllers\Controller;
use App\Services\PersonDetailTimelineService;
use App\Http\Requests\Api\PersonDetailTimeline\PersonDetailTimelineRequest;

class PersonDetailTimelineController extends Controller
{
    private $service;

    public function __construct(PersonDetailTimelineService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/person-detail-time-line/get-screen-data",
     *      operationId="getScreenDataTimeline",
     *      tags={"H02-S06"},
     *      summary="Get Screen Data",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      {
     *                          "definition_label": "面談",
     *                          "definition_value": "10"
     *                      },
     *                      {
     *                          "definition_label": "説明会",
     *                          "definition_value": "20"
     *                      },
     *                      {
     *                          "definition_label": "講演会",
     *                          "definition_value": "30"
     *                      },
     *                      {
     *                          "definition_label": "外部コンテンツ",
     *                          "definition_value": "40"
     *                      }
     *                  }
     *              }
     *          )
     *      )
     * )
     */
    public function getScreenDataTimeline()
    {
        try{
            $data = $this->service->getScreenData();
            return $this->responseSuccess("success", $data);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/person-detail-time-line/search",
     *      operationId="searchTimeline",
     *      tags={"H02-S06"},
     *      summary="Search Person Detail Timeline",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Search Person Detail Timeline",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="product_name",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="start_datetime",
     *                      type="string",
     *                      default="2020-12-01"
     *                  ),
     *                  @OA\Property(
     *                      property="end_datetime",
     *                      type="string",
     *                      default="2022-12-31"
     *                  ),
     *                  @OA\Property(
     *                      property="channel_type",
     *                      type="string",
     *                      default=""
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                         "start_datetime": "2021-12-01 10:20:58",
     *                         "detail": {
     *                             "channel_type_10": {
     *                                 {
     *                                     "start_datetime": "2021-12-01 10:20:58",
     *                                     "end_datetime": "2022-01-30 22:00:00",
     *                                     "channel_type": "10",
     *                                     "channel_id": 1,
     *                                     "channel_detail_id": 1,
     *                                     "product_name": "a",
     *                                     "note": "a",
     *                                     "off_label_flag": 1,
     *                                     "content_name": "a",
     *                                     "phase_name": "a",
     *                                     "reaction_name": "a",
     *                                     "detail_order": 1,
     *                                     "org_label": "11122",
     *                                     "user_cd": "10009",
     *                                     "user_name": "小峯 嘉彦",
     *                                     "user_post_type": "20",
     *                                     "title": "title 1",
     *                                     "facility_cd": "011010002",
     *                                     "facility_short_name": "a",
     *                                     "department_name": "a",
     *                                     "person_cd": "1",
     *                                     "person_name": "a",
     *                                     "display_position_name": "a",
     *                                     "convention_type_name": "a",
     *                                     "briefing_type_name": "a",
     *                                     "briefing_facility_short_name": "a",
     *                                     "content": null
     *                                 }
     *                             }
     *                         }
     *                     }
     *                 }
     *              )
     *      ),
     *      @OA\Response(response=400, description="Validation Error")
     * )
     */
    public function searchTimeline(PersonDetailTimelineRequest $request)
    {
        try{
            $data = $this->service->searchTimeline($request);
            return $this->responseSuccess('success', $data);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
    }
}
