<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecommendedPeriodConfirmationService;
use Illuminate\Http\Request;

class RecommendedPeriodConfirmationController extends Controller
{
    private $RecommendedPeriodConfirmationService;

    public function __construct(RecommendedPeriodConfirmationService $RecommendedPeriodConfirmationService)
    {
        $this->RecommendedPeriodConfirmationService = $RecommendedPeriodConfirmationService;
        $this->middleware('role.has:'.config('role.MASTER_MG.CODE').','.config('role.SYS_MG.CODE'));
    }
    /**
     * @OA\GET(
     *      path="/api/recommended-period-confirmation",
     *      operationId="getUserListRecommendedPeriodConfirmation",
     *      tags={"I01-S03"},
     *      summary="Get User List Recommended Period Confirmation",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                           "records": {
     *                               {
     *                                   "user_cd": "10001",
     *                                   "mail_address": "10001@dummy.ne.jp",
     *                                   "user_name": "西嶋 洋明",
     *                                   "advance_reservation": {
     *                                       {
     *                                           "definition_value": "16",
     *                                           "definition_label": "営業部門長",
     *                                           "user_post_type": "16",
     *                                           "org_label": "営業部門",
     *                                           "main_flag": "主"
     *                                       }
     *                                   },
     *                                   "role_information": {
     *                                       {
     *                                           "start_date": "2022/03/04",
     *                                           "end_date": "2022/03/29",
     *                                           "roles": {
     *                                               {
     *                                                   "role": "R01",
     *                                                   "role_name": "システム利用者"
     *                                               }
     *                                           }
     *                                       }
     *                                   },
     *                                   "organization_information": {
     *                                       {
     *                                           "start_date": "2022/01/08",
     *                                           "end_date": "2022/03/30",
     *                                           "organization": {
     *                                               {
     *                                                   "org_cd": "11212",
     *                                                   "definition_label": "MR",
     *                                                   "org_label": "関東北営栃木A",
     *                                                   "main_flag": 1,
     *                                                   "user_post_type": "10"
     *                                               }
     *                                           }
     *                                       }
     *                                   },
     *                                   "user_information": {
     *                                       {
     *                                           "user_name": "西嶋 洋明",
     *                                           "mail_address": "10001@dummy.ne.jp",
     *                                           "start_date": "2021-12-01",
     *                                           "end_date": "2024-05-10"
     *                                       }
     *                                   },
     *                                   "approval_knowledge": {
     *                                       {
     *                                           "start_date": "2022/01/22",
     *                                           "end_date": "9999/12/31",
     *                                           "approval_layer_num": {
     *                                               {
     *                                                   "approval_layer_num": 1,
     *                                                   "approval_user": {
     *                                                       {
     *                                                           "approval_user_cd": "10001",
     *                                                           "user_name": "西嶋 洋明",
     *                                                           "org_label": "関東北営栃木A",
     *                                                           "org_cd": "11212",
     *                                                           "definition_label": "MR"
     *                                                       }
     *                                                   }
     *                                               }
     *                                           }
     *                                       }
     *                                   },
     *                                   "approval_lecture": {
     *                                       {
     *                                           "start_date": "2022/01/22",
     *                                           "end_date": "9999/12/31",
     *                                           "approval_layer_num": {
     *                                               {
     *                                                   "approval_layer_num": 1,
     *                                                   "approval_user": {
     *                                                       {
     *                                                           "approval_user_cd": "10001",
     *                                                           "user_name": "西嶋 洋明",
     *                                                           "org_label": "関東北営栃木A",
     *                                                           "org_cd": "11212",
     *                                                           "definition_label": "MR"
     *                                                       }
     *                                                   }
     *                                               }
     *                                           }
     *                                       }
     *                                   },
     *                                   "approval_briefing": {
     *                                       {
     *                                           "start_date": "2022/03/04",
     *                                           "end_date": "9999/12/31",
     *                                           "approval_layer_num": {
     *                                               {
     *                                                   "approval_layer_num": 1,
     *                                                   "approval_user": {
     *                                                       {
     *                                                           "approval_user_cd": "10001",
     *                                                           "user_name": "西嶋 洋明",
     *                                                           "org_label": "関東北営栃木A",
     *                                                           "org_cd": "11212",
     *                                                           "definition_label": "MR"
     *                                                       },
     *                                                       {
     *                                                           "approval_user_cd": "10002",
     *                                                           "user_name": "安永 みづほ",
     *                                                           "org_label": "営業部門",
     *                                                           "org_cd": "10000",
     *                                                           "definition_label": "営業部門長"
     *                                                       }
     *                                                   }
     *                                               }
     *                                           }
     *                                       }
     *                                   },
     *           "approval_daily_report": {
     *               {
     *                   "start_date": "2022/01/21",
     *                   "end_date": "2022/03/30",
     *                   "approval_layer_num": {
     *                       {
     *                           "approval_layer_num": 1,
     *                           "approval_user": {
     *                               {
     *                                   "approval_user_cd": "10002",
     *                                   "user_name": "安永 みづほ",
     *                                   "org_label": "営業部門",
     *                                   "org_cd": "10000",
     *                                   "definition_label": "営業部門長"
     *                               }
     *                           }
     *                       }
     *                   }
     *               }
     *           }
     *       }
     *           },
     *           "pagination": {
     *               "total_items": 1,
     *               "total_pages": 1,
     *               "previous_page": 1,
     *               "next_page": 1,
     *               "current_page": 1,
     *               "first_page": 1,
     *               "last_page": 1
     *           }
     *       }
     *   }
     *                 
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function getUserList(Request $request)
    {
        $data =  $this->RecommendedPeriodConfirmationService->getData($request);
        return $this->responseSuccess("success", $data);
    }
}
