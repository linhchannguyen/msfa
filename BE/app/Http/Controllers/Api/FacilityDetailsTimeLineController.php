<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FacilityDetailsTimeLine\FacilityDetailsTimeLineRequest;
use App\Services\FacilityDetailsTimeLineService;
use Exception;

class FacilityDetailsTimeLineController extends Controller
{
    private $service;

    public function __construct(FacilityDetailsTimeLineService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     *  @OA\Get(
     *      path="/api/facility-details-time-line/get-screen-data",
     *      tags={"H01-S05"},
     *      summary="Facility Details Time Line",
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
     *                           "definition_label": "面談",
     *                           "definition_value": "10"
     *                       },
     *                       {
     *                           "definition_label": "説明会",
     *                           "definition_value": "20"
     *                       },
     *                       {
     *                           "definition_label": "講演会",
     *                           "definition_value": "30"
     *                       },
     *                       {
     *                           "definition_label": "外部コンテンツ",
     *                           "definition_value": "40"
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
     *      path="/api/facility-details-time-line",
     *      tags={"H01-S05"},
     *      summary="Facility Details Time Line",
     *      description="Get Screen Data",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="person_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="product_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_datetime",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="end_datetime",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="channel_type",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="timeline_id",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *       type="object",
     *       example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       {
     *                           "start_date": "2021/10/29",
     *                           "content": {
     *                                {
     *                                       "timeline_id": 70,
     *                                       "start_datetime": "2022-02-04 16:24:50",
     *                                       "end_datetime": "2022-02-04 19:24:50",
     *                                       "channel_type": "10",
     *                                       "definition_label": "面談",
     *                                       "channel_id": 392,
     *                                       "call_id": 392,
     *                                       "schedule_id": 2,
     *                                       "convention_id" : 1,
     *                                       "channel_detail": {
     *                                           {
     *                                               "channel_detail_id": 364,
     *                                               "product_name": "BBB製品",
     *                                               "note": null,
     *                                               "content_name": "WEB会議",
     *                                               "phase_name": null,
     *                                               "reaction_name": null,
     *                                               "detail_order": 1
     *                                           }
     *                                       },
     *                                       "off_label_flag": 0,
     *                                       "org_label": "営業部",
     *                                       "user_cd": "10004",
     *                                       "user_name": "西嶋 洋明",
     *                                       "user_post_type": "AL",
     *                                       "title": null,
     *                                       "facility_cd": "011010003",
     *                                       "facility_short_name": "田口医師会保健医療センター（札幌市中央区）",
     *                                       "department_name": "その他",
     *                                       "person_cd": "01101116",
     *                                       "person_name": "丸山 拓夫",
     *                                       "display_position_name": ""
     *                                   }
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
    public function getFacilityDetailsTimeLine(FacilityDetailsTimeLineRequest $request)
    {
        $facility_cd = $request->facility_cd;
        $person_cd = $request->person_cd;
        $product_cd = $request->product_cd;
        $start_datetime = $request->start_datetime;
        $end_datetime = $request->end_datetime;
        $channel_type = $request->channel_type;
        $data = $this->service->getFacilityDetailsTimeLine($facility_cd, $person_cd, $product_cd, $start_datetime, $end_datetime, $channel_type, null);
        return $this->responseSuccess('success', $data);
    }
}
