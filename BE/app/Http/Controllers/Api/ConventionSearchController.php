<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ConventionSearchService;
use App\Http\Requests\Api\ConventionSearch\ConventionAllRequest;
use App\Http\Requests\Api\ConventionSearch\ConventionDetailRequest;
use App\Http\Requests\Api\ConventionSearch\CancelSubmitConventionRequest;
use App\Http\Requests\Api\ConventionSearch\ApprovalRemandConventionRequest;
use App\Http\Requests\Api\ConventionSearch\CopyConventionDetailRequest;
use App\Http\Requests\Api\ConventionSearch\SaveConventionDetailRequest;
use App\Services\SystemParameterService;
use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;
use App\Repositories\DailyReport\DailyReportRepository;
use App\Repositories\Schedule\ScheduleRepositoryInterface;

use App\Services\AuthService;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConventionSearchController extends Controller
{
    private $service, $authService, $status_save_plan, $status_submit_plan, $pending_convention, $end_date, $schedule, $definition_label_convention_usage_type, $status_approval_finnal_plan;

    public function __construct(
        ConventionSearchService $service,
        SystemParameterService $system,
        ConventionSearchRepositoryInterface $repo,
        ScheduleRepositoryInterface $schedule,
        AuthService $authService
    ) {
        $this->service = $service;
        $this->schedule = $schedule;
        $this->system = $system;
        $this->repo = $repo;
        $this->authService = $authService;
        $this->status_save_plan = 10;
        $this->status_submit_plan = 20;
        $this->status_approval_finnal_plan = 30;
        $this->status_save_result = 40;
        $this->status_submit_result = 50;
        $this->pending_convention = 70;
        $this->end_date = "";
        $this->definition_label_convention_usage_type = "講演会";
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/convention-search",
     *      operationId="GetDataIndexConventionSearch",
     *      tags={"C01-S01"},
     *      summary="Get data index convention search",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "status_type":{
     *                          {
     *                           "status_type_value": "10",
     *                           "status_type_label": "計画入力中"
     *                          }
     *                      },
     *                      "convention_type":{
     *                          {
     *                           "convention_type_value": "10",
     *                           "convention_type_label": "医師・メディカルスタッフ向け講演会"
     *                          }
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function index()
    {
        $data = $this->service->getData();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/convention-search/list",
     *      operationId="GetDataConvention",
     *      tags={"C01-S01"},
     *      summary="Get list Convention",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="start_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="end_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="plan_org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="product_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "remand_flag": 0,
     *                      "start_date": "2019-04-01",
     *                      "start_time": "2021-04-04 10:00:00",
     *                      "end_time": "2021-04-04 10:30:00",
     *                      "status_type": null,
     *                      "convention_name": "test_7",
     *                      "convention_id": 7,
     *                      "convention_type": "医師・メディカルスタッフ向け講演会",
     *                      "product_name": "DDD製品カプセル",
     *                      "org_label": "東北北営業所",
     *                      "plan_org_label": "北海道営業所第2A"
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getData(ConventionAllRequest $request)
    {
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->allData($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/convention-search/index",
     *      operationId="GetDataDetail",
     *      tags={"C01-S02"},
     *      summary="Get data detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "status_type": {
     *                            {
     *                                "status_type": "10",
     *                                "status_type_label": "計画入力中"
     *                            },
     *                            {
     *                                "status_type": "20",
     *                                "status_type_label": "計画承認待ち"
     *                            },
     *                            {
     *                                "status_type": "30",
     *                                "status_type_label": "計画承認済"
     *                            },
     *                            {
     *                                "status_type": "40",
     *                                "status_type_label": "結果入力中"
     *                            },
     *                            {
     *                                "status_type": "50",
     *                                "status_type_label": "結果承認待ち"
     *                            },
     *                            {
     *                                "status_type": "60",
     *                                "status_type_label": "結果承認済"
     *                            },
     *                            {
     *                                "status_type": "70",
     *                                "status_type_label": "中止"
     *                            }
     *                        },
     *                        "convention_type": {
     *                            {
     *                                "convention_type": "10",
     *                                "convention_type_label": "医師・メディカルスタッフ向け講演会"
     *                            },
     *                            {
     *                                "convention_type": "12",
     *                                "convention_type_label": "医師・向け講演会"
     *                            },
     *                            {
     *                                "convention_type": "14",
     *                                "convention_type_label": "メディカルスタッフ向け講演会"
     *                            },
     *                            {
     *                                "convention_type": "20",
     *                                "convention_type_label": "学会地方回"
     *                            },
     *                            {
     *                                "convention_type": "30",
     *                                "convention_type_label": "医師会"
     *                            },
     *                            {
     *                                "convention_type": "40",
     *                                "convention_type_label": "エリア講演会"
     *                            },
     *                            {
     *                                "convention_type": "50",
     *                                "convention_type_label": "地方連携講演会"
     *                            },
     *                            {
     *                                "convention_type": "60",
     *                                "convention_type_label": "世話人会"
     *                            },
     *                            {
     *                                "convention_type": "70",
     *                                "convention_type_label": "社内研修会"
     *                            },
     *                            {
     *                                "convention_type": "80",
     *                                "convention_type_label": "その他"
     *                            }
     *                        },
     *                        "select_national_flag": false,
     *                        "hold_type": {
     *                            {
     *                                "hold_type_value": "10",
     *                                "hold_type_label": "初回・単回"
     *                            },
     *                            {
     *                                "hold_type_value": "20",
     *                                "hold_type_label": "継続"
     *                            }
     *                        },
     *                        "hold_method": {
     *                            {
     *                                "hold_method_value": "10",
     *                                "hold_method_label": "会場開催"
     *                            },
     *                            {
     *                                "hold_method_value": "20",
     *                                "hold_method_label": "WEB開催"
     *                            },
     *                            {
     *                                "hold_method_value": "30",
     *                                "hold_method_label": "会場開催・WEB開催"
     *                            }
     *                        },
     *                        "organization_layer": {
     *                            {
     *                                "organization_layer_value": "1",
     *                                "organization_layer_label": "部門"
     *                            },
     *                            {
     *                                "organization_layer_value": "2",
     *                                "organization_layer_label": "事業部"
     *                            },
     *                            {
     *                                "organization_layer_value": "3",
     *                                "organization_layer_label": "支店"
     *                            },
     *                            {
     *                                "organization_layer_value": "4",
     *                                "organization_layer_label": "営業所"
     *                            },
     *                            {
     *                                "organization_layer_value": "5",
     *                                "organization_layer_label": "エリア"
     *                            }
     *                        },
     *                        "hold_form": {
     *                            {
     *                                "hold_form_value": "10",
     *                                "hold_form_label": "主催"
     *                            },
     *                            {
     *                                "hold_form_value": "20",
     *                                "hold_form_label": "共催"
     *                            }
     *                        },
     *                        "cost_share_type": {
     *                            {
     *                                "cost_share_type_value": "10",
     *                                "cost_share_type_label": "全額当社"
     *                            },
     *                            {
     *                                "cost_share_type_value": "20",
     *                                "cost_share_type_label": "他社負担"
     *                            }
     *                        },
     *                        "expense_classification": {
     *                            {
     *                                "expense_classification_value": "10",
     *                                "expense_classification_label": "計画"
     *                            },
     *                            {
     *                                "expense_classification_value": "20",
     *                                "expense_classification_label": "結果"
     *                            }
     *                        }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getIndex()
    {
        $data = $this->service->getIndex();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/convention-search/detail",
     *      operationId="GetConventionDetail",
     *      tags={"C01-S02"},
     *      summary="Get Convention Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
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
    public function getConventionDetail(ConventionDetailRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getConventionDetail($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\POST(
     *      path="/api/convention-search/detail/copy",
     *      operationId="CopytConventionDetail",
     *      tags={"C01-S02"},
     *      summary="Copy Convention Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="start_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_time",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="end_time",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="cohost_partner_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="disposal_technique",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_form",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_method",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="parent_series_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="place",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="series_convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_product",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_product.*.product_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_product.*.product_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_target_org",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_target_org.*.org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_target_org.*.org_label",in="query", @OA\Schema(type="string")),
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
    public function copyConventionDetail(CopyConventionDetailRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->user_login = $this->getCurrentUser();
            $detail_user = $this->authService->getInfoUser($request->user_login);
            $request->user_name = $detail_user->user_name;
            $request->org_cd = $detail_user->org_cd;
            $request->org_label = $detail_user->org_label;
            $data = $this->service->copyConventionDetail($request);
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/convention-search/detail/selection-series",
     *      operationId="SeriesConventionDetail",
     *      tags={"C01-S02"},
     *      summary="Selection Series Convention Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
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
    public function seriesConventionDetail(ConventionDetailRequest $request)
    {
        if (!$this->checkDeadlinePlanConvention($request->start_date)) {
            return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
        }
        $data = $this->service->selectionSeriesConventionDetail($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\POST(
     *      path="/api/convention-search/detail/save-plan",
     *      operationId="savePlanConventionDetail",
     *      tags={"C01-S02"},
     *      summary="Save Plan Convention Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_time",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="end_time",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="cohost_partner_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="disposal_technique",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_form",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_method",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="parent_series_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="place",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="series_convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_product",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_product.*.product_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_product.*.delete_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_target_org",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_target_org.*.org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_target_org.*.delete_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_document",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_document.*.document_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_document.*.delete_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_file",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_file.*.file_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_file.*.file",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_file.*.delete_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.expense_item_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.plan_amount",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.result_amount",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.result_amount",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.expense_item_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_num",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.option_item_content",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.option_item_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.plan_amount",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.plan_quantity",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.plan_unit_price",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.result_amount",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.result_quantity",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.result_unit_price",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.expense_item_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.layer_num",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.option_item_content",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.option_item_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.plan_amount",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.plan_quantity",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.plan_unit_price",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.result_amount",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.result_quantity",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="area_expense.*.layer_2.*.layer_3.*.result_unit_price",in="query", @OA\Schema(type="string")),
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
    public function savePlanConventionDetail(SaveConventionDetailRequest $request)
    {
        DB::beginTransaction();
        try {
            set_time_limit(0);
            $convention_detail = $this->repo->getDetailConventionByConventionID($request);
            if (!$this->checkDeadlinePlanConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
            }
            $dateSystem = date_create($this->system->getCurrentSystemDateTime())->format('Y/m/d');
            $start_date = date("Y/m/d", strtotime($request->start_date));
            if ($request->convention_id) {
                if (($start_date != date_create($convention_detail->start_date)->format('Y/m/d')) && $start_date <= $dateSystem) {
                    return $this->responseErrorForbidden("過去日には登録できません。");
                }
            } else {
                if ($dateSystem >= $start_date) {
                    return $this->responseErrorForbidden("過去日には登録できません。");
                }
            }
            $request->user_login = $this->getCurrentUser();
            $request->status_type = $request->status_type ? $request->status_type : $this->status_save_plan;
            $detail_user = $this->authService->getInfoUser($request->user_login);
            $request->user_name = $detail_user->user_name;
            $request->org_cd = $detail_user->org_cd;
            $request->org_label = $detail_user->org_label;
            $data = $this->service->savePlanConventionDetail($request);
            $this->service->saveConventionPlanAreaExpense($request);
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/convention-search/detail/submit-plan",
     *      operationId="submitPlanConventionDetail",
     *      tags={"C01-S02"},
     *      summary="Submit Plan Convention Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="parent_series_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="series_convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_method",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="start_time",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="end_time",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="place",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="hold_purpose",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="remarks",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="hold_form",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="cohost_partner_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="cost_share_type",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="cost_share_content",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="disposal_technique",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="reason",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="note",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product.product_cd",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product.product_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.start_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.end_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.document_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.document_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.file_type",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.org_label",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.org_cd",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.layer_num",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file.display_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file.file_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.convention_attendee_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.facility_short_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.department_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.person_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.display_position_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.gratuity",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.withholding_tax",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.give_amount",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.subject",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="area_expense",in="query", @OA\Schema(type="date")),
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
    public function submitPlanConventionDetail(SaveConventionDetailRequest $request)
    {
        DB::beginTransaction();
        try {
            set_time_limit(0);
            $convention_detail = $this->repo->getDetailConventionByConventionID($request);
            if (!$this->checkDeadlinePlanConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
            }
            $dateSystem = date_create($this->system->getCurrentSystemDateTime())->format('Y/m/d');
            $start_date = date("Y/m/d", strtotime($request->start_date));
            if ($request->convention_id) {
                if (($start_date != date_create($convention_detail->start_date)->format('Y/m/d')) && $start_date <= $dateSystem) {
                    return $this->responseErrorForbidden("過去日には登録できません。");
                }
            } else {
                if ($dateSystem >= $start_date) {
                    return $this->responseErrorForbidden("過去日には登録できません。");
                }
            }
            $request->user_login = $this->getCurrentUser();
            $request->status_type = $this->status_submit_plan;
            $detail_user = $this->authService->getInfoUser($request->user_login);
            $request->user_name = $detail_user->user_name;
            $request->org_cd = $detail_user->org_cd;
            $request->org_label = $detail_user->org_label;
            $user_approval_knowledge = $this->service->getUserApproval($request->user_login);
            if (empty($user_approval_knowledge)) {
                return $this->responseErrorValidate(__('convention.no_approval'));
            }
            $convention_detail = $this->service->savePlanConventionDetail($request);
            if (isset($convention_detail->schedule_id)) {
                if ($this->checkStatusReport($convention_detail->schedule_id, $convention_detail->start_date)) {
                    if ($start_date != date_create($convention_detail->start_date)->format('Y/m/d')) {
                        return $this->responseErrorForbidden("日報が提出済のため、日付は変更できません。日報を差戻ししてから変更してください。");
                    }
                }
            }
            if (!($convention_detail->status_type != $this->status_save_plan || $convention_detail->status_type != $this->status_submit_plan)) {
                return $this->responseErrorForbidden("本講演会は承認されましたので提出はできません。");
            }
            if ($request->convention_id) {
                if (($start_date != date_create($convention_detail->start_date)->format('Y/m/d')) && $start_date <= $dateSystem) {
                    return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $dateSystem . "）です。");
                }
            }
            $request->convention_detail = $convention_detail;
            $this->service->saveConventionPlanAreaExpense($request);
            $this->service->submitPlanConventionDetail($request);
            DB::commit();
            return $this->responseCreated('success', []);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/convention-search/detail/save-result",
     *      operationId="saveResultConventionDetail",
     *      tags={"C01-S02"},
     *      summary="Save Result Convention Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="parent_series_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="series_convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_method",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="start_time",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="end_time",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="place",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="hold_purpose",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="remarks",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="hold_form",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="cohost_partner_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="cost_share_type",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="cost_share_content",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="disposal_technique",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="reason",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="note",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product.product_cd",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product.product_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.start_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.end_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.document_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.document_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.file_type",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.org_label",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.org_cd",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.layer_num",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file.display_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file.file_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.convention_attendee_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.facility_short_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.department_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.person_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.display_position_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.gratuity",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.withholding_tax",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.give_amount",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.subject",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="area_expense",in="query", @OA\Schema(type="date")),
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
    public function saveResultConventionDetail(SaveConventionDetailRequest $request)
    {
        DB::beginTransaction();
        try {
            set_time_limit(0);
            $convention_detail = $this->repo->getDetailConventionByConventionID($request);
            if (isset($convention_detail->schedule_id)) {
                if ($this->checkStatusReport($convention_detail->schedule_id, $convention_detail->start_date)) {
                    if (date_create($request->start_date)->format('Y-m-d') != date_create($convention_detail->start_date)->format('Y-m-d')) {
                        return $this->responseErrorForbidden("日報が提出済のため、日付は変更できません。日報を差戻ししてから変更してください。");
                    }
                }
            }
            if (!$this->checkDeadlinePlanConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
            }
            $dateSystem = date_create($this->system->getCurrentSystemDateTime())->format('Y/m/d');
            $start_date = date("Y/m/d", strtotime($request->start_date));
            if ($request->convention_id) {
                if (($start_date != date_create($convention_detail->start_date)->format('Y/m/d')) && $start_date <= $dateSystem) {
                    return $this->responseErrorForbidden("過去日には登録できません。");
                }
            }
            $request->convention_detail = $convention_detail;
            $request->user_login = $this->getCurrentUser();
            $request->status_type = $this->status_save_result;
            $detail_user = $this->authService->getInfoUser($request->user_login);
            $request->user_name = $detail_user->user_name;
            $request->org_cd = $detail_user->org_cd;
            $request->org_label = $detail_user->org_label;
            $data = $this->service->savePlanConventionDetail($request);
            $this->service->saveConventionResultAreaExpense($request);
            $document_review = $this->repo->getDocumentReview($request->convention_id, $this->repo->getDocumentUsageType($this->definition_label_convention_usage_type));
            $data->document_review = count($document_review) > 0 ? 0 : 1;
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/convention-search/detail/submit-result",
     *      operationId="submitResultConventionDetail",
     *      tags={"C01-S02"},
     *      summary="Submit Result Convention Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="parent_series_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="series_convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="hold_method",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="start_time",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="end_time",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="place",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="hold_purpose",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="remarks",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="hold_form",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="cohost_partner_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="cost_share_type",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="cost_share_content",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="disposal_technique",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="reason",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="note",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product.product_cd",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product.product_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_product.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.start_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.end_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.document_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.document_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.file_type",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_document.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.org_label",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.org_cd",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.layer_num",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_target_org.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file.display_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file.file_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_file.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.convention_attendee_id",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.facility_short_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.department_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.person_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.display_position_name",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.gratuity",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.withholding_tax",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.give_amount",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.subject",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="convention_attendee.delete_flag",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="area_expense",in="query", @OA\Schema(type="date")),
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
    public function submitResultConventionDetail(SaveConventionDetailRequest $request)
    {
        DB::beginTransaction();
        try {
            set_time_limit(0);
            $convention_detail = $this->repo->getDetailConventionByConventionID($request);
            if (isset($convention_detail->schedule_id)) {
                if ($this->checkStatusReport($convention_detail->schedule_id, $convention_detail->start_date)) {
                    if (date_create($request->start_date)->format('Y-m-d') != date_create($convention_detail->start_date)->format('Y-m-d')) {
                        return $this->responseErrorForbidden("日報が提出済のため、日付は変更できません。日報を差戻ししてから変更してください。");
                    }
                }
            }
            if (!$this->checkDeadlinePlanConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
            }
            if (!($convention_detail->status_type != $this->status_save_result || $convention_detail->status_type != $this->status_approval_finnal_plan)) {
                return $this->responseErrorForbidden("本講演会は承認されましたので提出はできません。");
            }
            $dateSystem = date_create($this->system->getCurrentSystemDateTime())->format('Y/m/d');
            $start_date = date("Y/m/d", strtotime($request->start_date));
            if ($request->convention_id) {
                if (($start_date != date_create($convention_detail->start_date)->format('Y/m/d')) && $start_date <= $dateSystem) {
                    return $this->responseErrorForbidden("過去日には登録できません。");
                }
            }
            $request->user_login = $this->getCurrentUser();
            $request->status_type = $this->status_submit_result;
            $request->convention_detail = $convention_detail;
            $detail_user = $this->authService->getInfoUser($request->user_login);
            $request->user_name = $detail_user->user_name;
            $request->org_cd = $detail_user->org_cd;
            $request->org_label = $detail_user->org_label;
            $data = $this->service->savePlanConventionDetail($request);
            $this->service->submitResultConventionDetail($request);
            $this->service->saveConventionResultAreaExpense($request);
            $document_review = $this->repo->getDocumentReview($request->convention_id, $this->repo->getDocumentUsageType($this->definition_label_convention_usage_type));
            $data->document_review = count($document_review) > 0 ? 0 : 1;
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/convention-search/detail/cancel-submit",
     *      operationId="CancelSubmitConvention",
     *      tags={"C01-S02"},
     *      summary="Cancel Submit Convention",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status_type",in="query", @OA\Schema(type="string")),
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
    public function cancelSubmitConvention(CancelSubmitConventionRequest $request)
    {
        DB::beginTransaction();
        try {
            $convention_detail = $this->repo->getDetailConventionByConventionID($request);
            if (isset($convention_detail->schedule_id)) {
                if ($this->checkStatusReport($convention_detail->schedule_id, $convention_detail->start_date)) {
                    return $this->responseErrorForbidden("日報が提出済のため、日付は変更できません。日報を差戻ししてから変更してください。");
                }
            }
            if (!$this->checkDeadlinePlanConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
            }
            if (!($convention_detail->status_type == $this->status_submit_plan || $convention_detail->status_type == $this->status_submit_result)) {
                return $this->responseErrorForbidden("本講演会は承認されましたので提出取消しはできません。");
            }
            $data = $this->service->cancelSubmitConvention($request);
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/convention-search/detail/remand",
     *      operationId="remandConvention",
     *      tags={"C01-S02"},
     *      summary="Remand Convention",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_name",in="query", @OA\Schema(type="string")),
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
    public function remandConvention(ApprovalRemandConventionRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->convention_detail = $this->repo->getDetailConventionByConventionID($request);
            if (!$this->checkDeadlinePlanConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
            }
            if (!($request->convention_detail->status_type == $this->status_submit_plan || $request->convention_detail->status_type == $this->status_submit_result)) {
                return $this->responseErrorForbidden("本講演会は承認されましたので差戻しはできません。");
            }
            $request->user_login =  $this->getCurrentUser();
            $data = $this->service->remandConvention($request);
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/convention-search/detail/approval",
     *      operationId="approvalConvention",
     *      tags={"C01-S02"},
     *      summary="Approval Convention",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_name",in="query", @OA\Schema(type="string")),
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
    public function approvalConvention(ApprovalRemandConventionRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->convention_detail = $this->repo->getDetailConventionByConventionID($request);
            if (!$this->checkDeadlinePlanConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
            }
            if (!($request->convention_detail->status_type == $this->status_submit_plan || $request->convention_detail->status_type == $this->status_submit_result)) {
                return $this->responseErrorForbidden("本講演会は承認されましたので承認はできません。");
            }
            $request->user_login =  $this->getCurrentUser();
            $data = $this->service->approvalConvention($request);
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/convention-search/detail/delete",
     *      operationId="deleteConvention",
     *      tags={"C01-S02"},
     *      summary="delete Convention Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
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
    public function deleteConvention(ConventionDetailRequest $request)
    {
        DB::beginTransaction();
        try {
            if (!$this->checkDeadlinePlanConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
            }
            $this->service->deleteConvention($request);
            DB::commit();
            return $this->responseNoContent('success');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/convention-search/detail/pending",
     *      operationId="pendingConvention",
     *      tags={"C01-S02"},
     *      summary="Pending Convention",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="convention_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="convention_name",in="query", @OA\Schema(type="string")),
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
    public function pendingConvention(ConventionDetailRequest $request)
    {
        DB::beginTransaction();
        try {
            $convention_detail = $this->repo->getDetailConventionByConventionID($request);
            if (!$this->checkDeadlinePlanConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が講演会入力期限外（" . $this->end_date . "）です。");
            }
            $request->convention_detail = $convention_detail;
            $request->status_type = $this->pending_convention;
            $data = $this->service->pendingConvention($request);
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    //check Status Report by ScheduleID
    public function checkStatusReport($schedule_id, $start_date)
    {
        $schedule = $this->schedule->getStatusReport($schedule_id, $start_date);
        $report_id = $schedule->report_id ?? null;
        $repo = new DailyReportRepository();
        $status = $repo->getStatusTypeReport($report_id);
        $status_report = false;
        if (isset($status->status_type)) {
            $status_report = $status->status_type == 1 ? true : false;
        }
        return $status_report;
    }

    public function checkDeadlinePlanConvention($date_input)
    {
        $dateSystem = date_create($this->system->getCurrentSystemDateTime())->format('Y/m/d');
        $input_deadline_convention = $this->repo->getInputDeadlineConvention($this->definition_label_convention_usage_type);
        $this->end_date = $dateSystem;
        if (isset($input_deadline_convention->parameter_value)) {
            $this->end_date = date("Y/m/d", strtotime("-" . $input_deadline_convention->parameter_value . " months", strtotime($dateSystem)));
        }
        if (($this->end_date >= date_create($date_input)->format('Y/m/d'))) {
            return false;
        }
        return true;
    }
}
