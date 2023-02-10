<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PersonalDetailsWorkingInformation\GetWorkingInformationRequest;
use App\Services\PersonalDetailsWorkingInformationService;
use Illuminate\Http\Request;
use Exception;

class PersonalDetailsWorkingInformationController extends Controller
{
    private $service;

    public function __construct(PersonalDetailsWorkingInformationService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     *  @OA\Get(
     *      path="/api/personal-details-working-information/get-screen-data",
     *      tags={"H02-S04"},
     *      summary="Personal Details",
     *      description="Get Screen Data",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *       type="object",
     *       example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       "phase_name": {
     *                           {
     *                               "phase_cd": "10",
     *                               "phase_name": "認知"
     *                           },
     *                           {
     *                               "phase_cd": "20",
     *                               "phase_name": "理解"
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
    public function getScreenData()
    {
        $data = $this->service->getScreenData();
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\Post(
     *      path="/api/personal-details-working-information",
     *      tags={"H02-S04"},
     *      summary="Personal Details",
     *      description="Get Personal Details Working Information",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="person_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *       type="object",
     *       example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       "records": {
     *                           {
     *                               "facility_short_name": "田口医師会保健医療センター（札幌市中央区）",
     *                               "segment": {
     *                                   {
     *                                       "segment_type": "10",
     *                                       "segment_name": "重要"
     *                                   }
     *                               },
     *                               "user_name": "金野 茉奈実",
     *                               "facility_cd": "011010002",
     *                               "user_cd": "10036",
     *                               "org_cd": "11110",
     *                               "org_label": "北海道営業所",
     *                               "item": {
     *                                   {
     *                                       "phase_cd": null,
     *                                       "product_cd": null,
     *                                       "product_name": null
     *                                   }
     *                               }
     *                           }
     *                       },
     *                       "pagination": {
     *                           "total_items": 1,
     *                           "total_pages": 1,
     *                           "previous_page": 1,
     *                           "next_page": 1,
     *                           "current_page": 1,
     *                           "first_page": 1,
     *                           "last_page": 1
     *                       }
     *                   }
     *               }
     *           )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getWorkingInformation(GetWorkingInformationRequest $request)
    {
        $person_cd = $request->person_cd;
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $data = $this->service->getWorkingInformation($person_cd, $limit, $offset);
        return $this->responseSuccess('success', $data);
    }
}
