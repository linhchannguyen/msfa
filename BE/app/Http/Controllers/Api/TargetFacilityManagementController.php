<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\SystemParameterService;
use App\Services\TargetFacilityManagementService;

class TargetFacilityManagementController extends Controller
{
    private $service, $system;

    public function __construct(TargetFacilityManagementService $service ,SystemParameterService $system)
    {
        $this->service = $service;
        $this->system = $system;
        $this->middleware('role.has:'.config('role.CALL_IMPLEMENTER.CODE').','.config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\GET(
     *    path="/api/target-facility-management/get-screen-data",
     *    operationId="getScreenDataTargetFacilityManagement",
     *    tags={"G01-S01"},
     *    summary="Target Facility Management",
     *    description="Get Screen Data",
     *    security={{"jwt": {}}},
     *    @OA\Response(response=200, description="Response successfully",
     *           @OA\JsonContent(
     *            type="object",
     *            example={
     *                     "status": "200",
     *                     "message": "success",
     *                     "data": {
     *                         {
     *                             "target_product_cd": "ABC",
     *                             "target_product_name": "ターゲットABC"
     *                         },
     *                         {
     *                             "target_product_cd": "DDD",
     *                             "target_product_name": "ターゲットDDD"
     *                         }
     *                     }
     *                 }
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
     *    path="/api/target-facility-management",
     *    operationId="targetFacilityManagement",
     *    tags={"G01-S01"},
     *    summary="Target Facility Management",
     *    description="Target Facility Management",
     *    security={{"jwt": {}}},
     *    @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="order_value",in="query", @OA\Schema(type="string")),
     *    @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *    @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *    @OA\Response(response=200, description="Response successfully",
     *           @OA\JsonContent(
     *            type="object",
     *            example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       "records": {
     *                           {
     *                               "facility_cd": "011080005",
     *                               "facility_name": "国立大学法人村上大学医学部附属病院（札幌市厚別区）",
     *                               "target_product": {
     *                                   {
     *                                       "target_product_cd": "ABC",
     *                                       "sub_target": "TEST",
     *                                       "number_person_cd": 2,
     *                                       "person_cd": {
     *                                           {
     *                                               "person_cd": "01101002"
     *                                           },
     *                                           {
     *                                               "person_cd": "01101033"
     *                                           }
     *                                       }
     *                                   },
     *                                   {
     *                                       "target_product_cd": "DDD",
     *                                       "sub_target": "TEST",
     *                                       "number_person_cd": 1,
     *                                       "person_cd": {
     *                                           {
     *                                               "person_cd": "01101006"
     *                                           }
     *                                       }
     *                                   }
     *                               }
     *                           },
     *                           {
     *                               "facility_cd": "011020005",
     *                               "facility_name": "国立大学法人村上大学医学部附属病院（札幌市北区）",
     *                               "target_product": {}
     *                           }
     *                       },
     *                       "pagination": {
     *                           "total_items": 640,
     *                           "total_pages": 7,
     *                           "previous_page": 1,
     *                           "next_page": 2,
     *                           "current_page": 1,
     *                           "first_page": 1,
     *                           "last_page": 7
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
    public function targetFacilityManagement(Request $request)
    {
        $user_cd  = $request->user_cd;
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $user_cd_login =  $this->getCurrentUser();
        $user_cd_param = !empty($user_cd) ? $user_cd : $user_cd_login;
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        $order_value = $request->order_value;
        $data =  $this->service->targetFacilityManagement($user_cd_param,$date_system,$order_value,$limit,$offset);
        return $this->responseSuccess('success', $data);
    }
}
