<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Exception;
use Carbon\Carbon;
use App\Services\HomeService;
use App\Traits\DateTimeTrait;
use Illuminate\Support\Facades\DB;
use App\Services\RolePolicyService;
use App\Http\Controllers\Controller;
use App\Transformers\HomeInformTransformer;
use App\Transformers\HomeMessageTransformer;
use App\Http\Requests\Api\Home\BeforeSalesResultsRequest;
use App\Http\Requests\Api\Home\ActualDigestionProcessRequest;
use App\Http\Requests\Api\Home\ActualDigestionRankingRequest;
use App\Services\OrganizationService;

class HomeController extends Controller
{
    use DateTimeTrait;
    private $service, $role, $org_service;

    public function __construct(HomeService $service, RolePolicyService $role, OrganizationService $org_service)
    {
        $this->org_service = $org_service;
        $this->service = $service;
        $this->role = $role;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/widget",
     *      operationId="getScreenDataWidget",
     *      tags={"Z02-S01"},
     *      summary="Get screen data",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                          "widget":{{
     *                              "widget_id": 1,
     *                              "widget_type": "W01",
     *                              "widget_type_id": null,
     *                              "widget_title": "否認・差戻しあり",
     *                              "sort_order": 1,
     *                              "height": 100,
     *                              "width": 100
     *                          }},
     *                          "organization_layer":{{
     *                              "definition_value": "1",
     *                              "definition_label": "支店"
     *                          }},
     *                          "actual_digestion_items":{{
     *                              "actual_sales_product_cd": "ABC",
     *                              "actual_sales_product_name": "実消化ABC"
     *                          }},
     *                     }
     *                 }
     *              )
     *        )
     * )
     */
    public function getScreenData()
    {
        try {
            $data = $this->service->getScreenData();
            return $this->responseSuccess('success', $data);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('messages.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/widget/message",
     *      operationId="getMessageList",
     *      tags={"Z02-S01"},
     *      summary="メッセージ",
     *      description="A-c．メッセージ",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                         "messages":{
     *                             {
     *                               "message_id": 3,
     *                               "message_subject": "subjesubjesubjesubjesubjesubj2",
     *                               "release_start_date": "2021-10-22",
     *                               "release_end_date": "2021-10-22",
     *                               "release_org_cd": "o3",
     *                               "org_name": "QA",
     *                               "sender_name": "sender_namesender_namesender_names",
     *                               "message_contents": "contents",
     *                               "important_flag": 0,
     *                               "last_update_datetime": "2021-10-29 12:17:10",
     *                               "create_user_cd": "4",
     *                               "message_read": 0
     *                             }
     *                         },
     *                         "important":{
     *                             {
     *                                "message_id": 17,
     *                                "message_subject": "subjesubjesubjesubjesubjesubj",
     *                                "release_start_date": "2021-10-20",
     *                                "release_end_date": "2021-10-19",
     *                                "release_org_cd": "10000",
     *                                "org_name": "営業部門",
     *                                "sender_name": "sender_namesender_namesender_names",
     *                                "message_contents": "contents",
     *                                "important_flag": 1,
     *                                "last_update_datetime": "2021-11-03 10:18:59",
     *                                "create_user_cd": "99",
     *                                "message_read": 0
     *                             }
     *                         },
     *                         "unread":{
     *                             {
     *                               "message_id": 19,
     *                               "message_subject": "subjesubjesubjesubjesubjesubj",
     *                               "release_start_date": "2021-10-22",
     *                               "release_end_date": "2021-10-22",
     *                               "release_org_cd": "10000",
     *                               "org_name": "営業部門",
     *                               "sender_name": "sender_namesender_namesender_names",
     *                               "message_contents": "<button>ASB</button>",
     *                               "important_flag": 0,
     *                               "last_update_datetime": "2021-11-03 15:14:34",
     *                               "create_user_cd": "99",
     *                               "message_read": 0
     *                             }
     *                         }
     *                     }
     *                 }
     *              )
     *        )
     * )
     */
    public function getMessageList()
    {
        $orgs = $this->org_service->getListData()['org_obj'] ?? [];
        $user_org = $this->getOrgCurrentUser($this->getCurrentUser());
        $results = $this->service->getMessageList($this->getCurrentUser(), $user_org, $orgs);
        $result['messages'] = empty($results['messages']) ? [] : HomeMessageTransformer::collection($results['messages']);
        $result['important'] = empty($results['important']) ? [] : HomeMessageTransformer::collection($results['important']);
        $result['unread'] = empty($results['unread']) ? [] : HomeMessageTransformer::collection($results['unread']);
        return $this->responseSuccess('success', $result);
    }

    /**
     * @OA\POST(
     *      path="/api/widget/message/{id}",
     *      operationId="readMessage",
     *      tags={"Z02-S01"},
     *      summary="Read message",
     *      description="A-c．メッセージ",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Read message",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="message_id",
     *                      type="integer",
     *                      description="The message id",
     *                      default="1"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response")
     * )
     */
    public function readMessage($id)
    {
        DB::beginTransaction();
        $data = new stdClass;
        $data->id = $id;
        $data->user_cd = $this->getCurrentUser();
        $CheckRead = $this->service->checkRead($data);
        if (empty($CheckRead)) {
            $result = $this->service->readMessage($data);
            if (!$result) {
                DB::rollBack();
                return $this->responseSystemError(__('messages.system_error'));
            }
            DB::commit();
        }
        return $this->responseCreated(__('messages.update_successfully'));
    }

    /**
     * @OA\GET(
     *      path="/api/widget/inform",
     *      operationId="getInformList",
     *      tags={"Z02-S01"},
     *      summary="ユーザ通知",
     *      description="C．ユーザ通知",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *           @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  "unarchived_inform": {
     *                      {
     *                          "inform_id": 1,
     *                          "inform_category_cd": "10",
     *                          "inform_datetime": "2021-11-01 14:53:54",
     *                          "inform_user_cd": "1",
     *                          "inform_contents": "Noti",
     *                          "archive_flag": 0,
     *                          "informed_flag": 0,
     *                          "read_flag": 0,
     *                          "url": ""
     *                      }
     *                  },
     *                  "uninformed_quantity": 1
     *                  }
     *              }
     *           )
     *      )
     * )
     */
    public function getInformList()
    {
        try {
            $data = $this->service->informList($this->getCurrentUser());
            $results['unarchived_inform'] = empty($data) ? [] : HomeInformTransformer::collection($data);
            $results['uninformed_quantity'] = $this->service->countInform($this->getCurrentUser())[0]->uninformed_quantity;
            return $this->responseSuccess('success', $results);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('messages.system_error'));
        }
    }

    /**
     * @OA\PUT(
     *      path="/api/widget/informed",
     *      operationId="informed",
     *      tags={"Z02-S01"},
     *      summary="未確認の通知が存在する場合は全て確認済に更新する。",
     *      description="C．ユーザ通知",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=201, description="Successful response")
     * )
     */
    public function informed()
    {
        DB::beginTransaction();
        try {
            $this->service->informed($this->getCurrentUser());
            DB::commit();
            return $this->responseCreated('success');
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('messages.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/widget/activity-plan",
     *      operationId="sameDayActivityPlan",
     *      tags={"Z02-S01"},
     *      summary="ユーザ通知",
     *      description="C．ユーザ通知",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *           @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{{
     *                      "schedule_id": 4153,
     *                      "schedule_type": "50",
     *                      "start_time": "2022-03-29 07:00:00",
     *                      "end_time": "2022-03-29 08:00:00",
     *                      "call_list": {{}},
     *                      "briefing_list": {{}},
     *                      "convention_list": {{}},
     *                      "private_list": {
     *                          {
     *                              "schedule_id": 4153,
     *                              "title": "プライベート"
     *                          }
     *                      },
     *                      "office_schedule_list": {{}}
     *                  }}
     *              }
     *           )
     *      )
     * )
     */
    public function sameDayActivityPlan()
    {
        try {
            $conditions['current'] = $this->currentJapanDateTime('Y-m-d');
            $conditions['user_cd'] = $this->getCurrentUser();
            $roles = $this->role->getRoleList($conditions['user_cd']);
            // ◆面談実施者の場合
            //     ウィジェットを表示する。
            if (!in_array(config('role.CALL_IMPLEMENTER.CODE'), $roles)) {
                return $this->responseSuccess('success', []);
            }
            $data = $this->service->getActivityPlan($conditions);
            return $this->responseSuccess('success', $data);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('messages.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/widget/external-url",
     *      operationId="externalURL",
     *      tags={"Z02-S01"},
     *      summary="外部URL",
     *      description="A-d．外部URL",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *           @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{{
     *                  }}
     *              }
     *           )
     *      )
     * )
     */
    public function externalURL()
    {
        try {
            $data = $this->service->getExternalUrl();
            return $this->responseSuccess('success', $data);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('messages.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/widget/non-submission",
     *      operationId="nonSubmission",
     *      tags={"Z02-S01"},
     *      summary="未提出通知",
     *      description="A-a 未提出通知",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *           @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "unsubmitted": 1,
     *                      "unapproved": 1,
     *                      "briefing_unapproved": 4,
     *                      "convention_unapproved": 8,
     *                      "knowledge_unapproved": {
     *                          {
     *                              "total_record": 5,
     *                              "knowledge_id_list": "549,549,549,591,592"
     *                          }
     *                      },
     *                      "daily_report_remand": 42,
     *                      "briefing_remand": 2,
     *                      "convention_remand": 2,
     *                      "knowledge_remand": {
     *                          {
     *                              "total_record": 6,
     *                              "knowledge_id_list": "1,2,314,317,546,549"
     *                          }
     *                      },
     *                      "inappropriate_report": 41,
     *                      "person_unconfirm": 25,
     *                      "facility_unconfirm": 6
     *                  }
     *              }
     *           )
     *      )
     * )
     */
    public function nonSubmission()
    {
        try {
            $conditions['user_cd'] = $this->getCurrentUser();
            $data = $this->service->getNonSubmissionReport($conditions);
            return $this->responseSuccess('success', $data);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('messages.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/widget/actual-digestion-ranking",
     *      operationId="actualDigestionRanking",
     *      tags={"Z02-S01"},
     *      summary="実消化ランキング",
     *      description="A-e-1 -> A-e-3: 実消化ランキング",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="aggregate_layer_num",in="query", @OA\Schema(type="string"), example="1"),
     *      @OA\Response(response=200, description="Successful response",
     *           @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{{
     *                      "actual_sales_product_name": "実消化DDD",
     *                      "sales_amount": "1317000.00"
     *                  }}
     *              }
     *           )
     *      )
     * )
     */
    public function actualDigestionRanking(ActualDigestionRankingRequest $request)
    {
        try {
            $conditions['user_cd'] = $this->getCurrentUser();
            $conditions['aggregate_layer_num'] = $request->aggregate_layer_num;
            $data = $this->service->actualDigestionRanking($conditions);
            return $this->responseSuccess('success', $data);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('messages.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/widget/before-sales-results",
     *      operationId="sameAsBeforeSalesResults",
     *      tags={"Z02-S01"},
     *      summary="前同売上実績",
     *      description="A-e-5 -> A-e-7 前同売上実績",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="aggregate_layer_num",in="query", @OA\Schema(type="string"), example="1"),
     *      @OA\Parameter(name="actual_sales_product_cd",in="query", @OA\Schema(type="string"), example="ABC"),
     *      @OA\Response(response=200, description="Successful response",
     *           @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{{
     *                      "sales_month": "202104",
     *                      "sales_amount": 0,
     *                      "previous_year_sales_amount": 0
     *                  }}
     *              }
     *           )
     *      )
     * )
     */
    public function sameAsBeforeSalesResults(BeforeSalesResultsRequest $request)
    {
        try {
            $conditions['user_cd'] = $this->getCurrentUser();
            $conditions['actual_sales_product_cd'] = $request->actual_sales_product_cd;
            $conditions['aggregate_layer_num'] = $request->aggregate_layer_num;
            $data = $this->service->sameAsBeforeSalesResults($conditions);
            return $this->responseSuccess('success', $data);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('messages.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/widget/actual-digestion-process",
     *      operationId="actualDigestionProcess",
     *      tags={"Z02-S01"},
     *      summary="実消化進捗状況",
     *      description="A-e-8 -> A-e-10 実消化進捗状況",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="aggregate_layer_num",in="query", @OA\Schema(type="string"), example="1"),
     *      @OA\Response(response=200, description="Successful response",
     *           @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{{
     *                     "sales_amount": "1154000.00",
     *                     "goal_amount": "1337000.00",
     *                     "actual_sales_product_name": "実消化ABC"
     *                  }}
     *              }
     *           )
     *      )
     * )
     */
    public function actualDigestionProcess(ActualDigestionProcessRequest $request)
    {
        try {
            $conditions['user_cd'] = $this->getCurrentUser();
            $conditions['aggregate_layer_num'] = $request->aggregate_layer_num;
            $data = $this->service->actualDigestionProcess($conditions);
            return $this->responseSuccess('success', $data);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('messages.system_error'));
        }
    }
}
