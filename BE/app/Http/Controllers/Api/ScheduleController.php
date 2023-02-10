<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\DateTimeTrait;
use App\Services\ScheduleService;
use Illuminate\Support\Facades\DB;
use App\Services\RolePolicyService;
use App\Http\Controllers\Controller;
use App\Services\OrganizationService;
use App\Http\Requests\Api\Schedule\AddVisitRequest;
use App\Http\Requests\Api\Schedule\SearchStockRequest;
use App\Http\Requests\Api\Schedule\CopyScheduleRequest;
use App\Http\Requests\Api\Schedule\GetScheduletRequest;
use App\Http\Requests\Api\Schedule\GetScreenDataRequest;
use App\Http\Requests\Api\Schedule\UpdateScheduleRequest;
use App\Http\Requests\Api\Schedule\AddFacilitySelectRequest;
use App\Http\Requests\Api\Schedule\AddOtherThanInterviewRequest;
use App\Http\Requests\Api\Schedule\AddFacilityPersonSelectRequest;
use App\Services\SystemParameterService;

class ScheduleController extends Controller
{
    use DateTimeTrait;
    private $service, $organization_service, $role, $system;

    public function __construct(ScheduleService $service, OrganizationService $organization_service, RolePolicyService $role, SystemParameterService $system)
    {
        $this->service = $service;
        $this->organization_service = $organization_service;
        $this->role = $role;
        $this->system = $system;
        $this->middleware('role.has:' . config('role.CALL_IMPLEMENTER.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/schedule/get-screen-data-schedule",
     *      operationId="getScreenDataSchedule",
     *      tags={"A01-S01"},
     *      summary="Get screen data.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_type",
     *                      type="string",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="string",
     *                      default="2021-12-27"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {
     *                      "target_product": {
     *                      {
     *                          "target_product_cd": "IIJ",
     *                          "target_product_name": "ターゲットIJ"
     *                      }},
     *                      "facility_person_segment_type": {
     *                      {
     *                          "segment_type": "10",
     *                          "segment_name": "重要"
     *                      }},
     *                      "display_option": {
     *                      {
     *                          "display_option_type": "A01",
     *                          "display_option_name": "ている他者の面談",
     *                          "background_color": "#E7E8FF",
     *                          "icon": "null",
     *                          "frame_border_color": "null"
     *                      }}
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getScreenDataSchedule(GetScreenDataRequest $request)
    {
        try {
            $user_cd =  $this->getCurrentUser();
            //CR No.37: 面談、社内予定、説明会は不可です。講演会は可能です。
            if (!empty($request->start_date)) {
                $conditions['start_date'] = $request->start_date;
                $conditions['end_date'] = $request->start_date;
                $conditions['user_cd'] = $user_cd;
                $conditions['schedule_type'] = $request->schedule_type;
                $report_status = $this->service->reportStatusListAdd($conditions);
                if (!$report_status) {
                    return  $this->responseErrorValidate(__('schedule.uncreate'));
                }
            }
            $result = $this->service->getScreenData();
            return $this->responseSuccess('success', $result);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/schedule/get-schedule",
     *      operationId="getScheduleListByUserLogin",
     *      tags={"A01-S01"},
     *      summary="Get Schedule List.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_date_from",
     *                      type="string",
     *                      description="schedule date from",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="schedule_date_to",
     *                      type="string",
     *                      description="schedule date to",
     *                      default=""
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {{
     *                      "schedule_id": 21,
     *                      "schedule_type": "10",
     *                      "start_date": "2021-12-27",
     *                      "start_time": "2021-12-27 17:21:00",
     *                      "end_time": "2021-12-27 18:21:00",
     *                      "title": "足立クリニック（厚岸町）",
     *                      "all_day_flag": 0,
     *                      "display_option_type": "V99",
     *                      "display_option_name": "訪問（※面談データがない場合",
     *                      "background_color": "#D8D9FF",
     *                      "icon": "null",
     *                      "frame_border_color": "null",
     *                      "user_cd": "10009"
     *                  }}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getScheduleListByUserLogin(GetScheduletRequest $request)
    {
        try {
            $user_cd =  $this->getCurrentUser();
            $conditions['user_cd'] = $user_cd;
            $conditions['schedule_date_from'] = $request->schedule_date_from;
            $conditions['schedule_date_to'] = $request->schedule_date_to;
            $result = $this->service->getScheduleListByUserLogin($conditions);
            return $this->responseSuccess('success', $result);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/schedule/copy-schedule",
     *      operationId="copySchedule",
     *      tags={"A01-S01"},
     *      summary="Copy schedule.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_date_from",
     *                      type="string",
     *                      default="2021-12-12"
     *                  ),
     *                  @OA\Property(
     *                      property="schedule_date_to",
     *                      type="string",
     *                      default="2022-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="string",
     *                      default="2022-01-03"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "201",
     *                  "message": "success",
     *                  "data": {}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function copySchedule(CopyScheduleRequest $request)
    {
        try {
            DB::beginTransaction();
            $user_cd =  $this->getCurrentUser();
            $current = $this->currentJapanDateTime('Y-m-d');
            $conditions['user_cd'] = $user_cd;
            $conditions['schedule_date_from'] = $request->schedule_date_from;
            $conditions['schedule_date_to'] = $request->schedule_date_to;
            $schedule_list = $this->service->getScheduleListByUserLogin($conditions);
            $start_date_copy = $request->start_date;
            $negative = 1;
            if ($start_date_copy < $request->schedule_date_from) {
                $negative = -1;
            }
            foreach ($schedule_list as $schedule) {
                if ($schedule->schedule_type == 10) {
                    $plan_flag = 0;
                    $act_flag = 0;
                    if ($request->start_date > $current) {
                        $plan_flag = 1;
                    }
                    $display_option_type = '';
                    if ($plan_flag == 0 && $act_flag == 0) {
                        $display_option_type = 'C00';
                    } else if ($plan_flag == 1 && $act_flag == 0) {
                        $display_option_type = 'C01';
                    } else if ($plan_flag == 0 && $act_flag == 1) {
                        $display_option_type = 'C02';
                    } else {
                        $display_option_type = 'C03';
                    }
                    $reportConditions['start_date'] = $schedule->start_date;
                    $reportConditions['end_date'] = $schedule->start_date;
                    $reportConditions['user_cd'] = $schedule->user_cd;
                    $reportConditions['schedule_type'] = $schedule->schedule_type;
                    $report_status = $this->service->reportStatusListAdd($reportConditions);
                    if (!$report_status) {
                        continue;
                    }
                    $start_date = Carbon::parse($start_date_copy)->diffInDays(Carbon::parse($request->schedule_date_from));
                    $start_time = Carbon::parse($start_date_copy)->diffInDays(Carbon::parse($request->schedule_date_from));
                    $end_time = Carbon::parse($start_date_copy)->diffInDays(Carbon::parse($request->schedule_date_from));
                    $schedule->start_date = Carbon::parse($schedule->start_date)->addDays($start_date * $negative)->format('Y-m-d');
                    $schedule->start_time = Carbon::parse($schedule->start_time)->addDays($start_time * $negative)->format('Y-m-d H:i:s');
                    $schedule->end_time = Carbon::parse($schedule->end_time)->addDays($end_time * $negative)->format('Y-m-d H:i:s');
                    //Insert t_schedule table
                    $schedule_copy['schedule_type'] = $schedule->schedule_type;
                    $schedule_copy['start_date'] = $schedule->start_date;
                    $schedule_copy['start_time'] = $schedule->start_time;
                    $schedule_copy['end_time'] = $schedule->end_time;
                    $schedule_copy['title'] = $schedule->title;
                    $schedule_copy['all_day_flag'] = $schedule->all_day_flag;
                    $schedule_copy['display_option_type'] = $display_option_type;
                    $schedule_copy['user_cd'] = $schedule->user_cd;
                    $this->service->addSchedule($schedule_copy);
                    $last_index_schedule = $this->service->lastInsertedSchedule();
                    //Insert t_visit table
                    $visits = $this->service->getVisitByScheduleId($schedule->schedule_id);
                    foreach ($visits as $visit) {
                        $visit_copy['schedule_id'] = $last_index_schedule->schedule_id;
                        $visit_copy['important_flag'] = $visit->important_flag ?? 0;
                        $visit_copy['facility_cd'] = $visit->facility_cd;
                        $visit_copy['facility_short_name'] = $visit->facility_short_name ?? "";
                        $visit_copy['user_cd'] = $visit->user_cd;
                        $visit_copy['user_name'] = $visit->user_name ?? "";
                        $visit_copy['org_cd'] = $visit->org_cd;
                        $visit_copy['org_short_name'] = $visit->org_short_name ?? "";
                        $visit_copy['remarks'] = $visit->remarks ?? "";
                        $this->service->addVisit($visit_copy);
                        $calls = $this->service->getCallByVisitId($visit->visit_id);
                        foreach ($calls as $call) {
                            //Insert t_call table
                            $last_index_visit = $this->service->lastInsertedVisit();
                            $call_copy['visit_id'] = $last_index_visit->visit_id;
                            $call_copy['person_cd'] = $call->person_cd;
                            $call_copy['plan_flag'] = $plan_flag;
                            $call_copy['act_flag'] = $act_flag;
                            $call_copy['person_name'] = $call->person_name;
                            $call_copy['department_cd'] = $call->department_cd;
                            $call_copy['department_name'] = $call->department_name;
                            $call_copy['display_position_name'] = $call->display_position_name;
                            $call_copy['off_label_flag'] = $call->off_label_flag;
                            $call_copy['off_label_concent_flag'] = $call->off_label_concent_flag;
                            $call_copy['off_label_call_time'] = $call->off_label_call_time;
                            $call_copy['related_product'] = $call->related_product ?? null;
                            $call_copy['question'] = $call->question ?? null;
                            $call_copy['answer'] = $call->answer ?? null;
                            $call_copy['re_question'] = $call->re_question ?? null;
                            $call_copy['literature'] = $call->literature ?? null;
                            $this->service->addCall($call_copy);
                        }
                    }
                }
            }
            DB::commit();
            return $this->responseCreated(__('schedule.create_successfully'));
        } catch (Exception $ex) {
            throw $ex;
            DB::rollBack();
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/schedule/search-visit",
     *      operationId="searchVisit",
     *      tags={"A01-S01"},
     *      summary="Get Visit List.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      description="user cd",
     *                      default="10162"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_name",
     *                      type="string",
     *                      description="facility name",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="person_name",
     *                      type="string",
     *                      description="person name",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="target_product_cd",
     *                      type="string",
     *                      description="target product cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="segment_type",
     *                      type="string",
     *                      description="segment type",
     *                      default=""
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {{
     *                      "user_cd": "10162",
     *                      "facility_cd": "016620070",
     *                      "person_cd": "01662083",
     *                      "facility_short_name": "足立クリニック（厚岸町）",
     *                      "person_name": "柏木 知佳",
     *                      "department_cd": "9999",
     *                      "department_name": "その他",
     *                      "position_cd": "062",
     *                      "position_name": "院長"
     *                  }}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function searchVisit(Request $request)
    {
        try {
            if (empty($request->user_cd) && empty($request->person_name) && empty($request->facility_name) && empty($request->segment_type) && empty($request->target_product_cd)) {
                return $this->responseErrorValidate(__('schedule.input_required'));
            }
            $request['limit'] = $request->filled('limit') ? $request->limit : 100;
            $request['offset'] = $request->filled('offset') ? $request->offset : 0;
            $result = $this->service->getVisitList($request);
            return $this->responseSuccess('success', $result);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/schedule/add-visit",
     *      operationId="addVisit",
     *      tags={"A01-S01"},
     *      summary="Add visit.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_type",
     *                      type="string",
     *                      description="schedule type",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="string",
     *                      description="start date",
     *                      default="2021-12-27"
     *                  ),
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="string",
     *                      description="start time",
     *                      default="2021-12-27 17:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="string",
     *                      description="end time",
     *                      default="2021-12-27 18:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      description="title",
     *                      default="足立クリニック（厚岸町）"
     *                  ),
     *                  @OA\Property(
     *                      property="all_day_flag",
     *                      type="string",
     *                      description="all day flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="display_option_type",
     *                      type="string",
     *                      description="display option type",
     *                      default="V99"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="facility cd",
     *                      default="011010018"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      description="facility short name",
     *                      default="藤川医院　医療（札幌市中央区）"
     *                  ),
     *                  @OA\Property(
     *                      property="plan_flag",
     *                      type="string",
     *                      description="plan flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="act_flag",
     *                      type="string",
     *                      description="act flag",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      description="person cd",
     *                      default="01101159"
     *                  ),
     *                  @OA\Property(
     *                      property="person_name",
     *                      type="string",
     *                      description="person name",
     *                      default="倉田 君吉"
     *                  ),
     *                  @OA\Property(
     *                      property="department_cd",
     *                      type="string",
     *                      description="department cd",
     *                      default="0641"
     *                  ),
     *                  @OA\Property(
     *                      property="department_name",
     *                      type="string",
     *                      description="department name",
     *                      default="糖尿病・内分泌・代謝内科"
     *                  ),
     *                  @OA\Property(
     *                      property="display_position_name",
     *                      type="string",
     *                      description="display position name",
     *                      default="役職なし"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "201",
     *                  "message": "success",
     *                  "data": {}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function addVisit(AddVisitRequest $request)
    {
        DB::beginTransaction();
        try {
            $current = $this->currentJapanDateTime('Y-m-d');
            $user_login = IS_USER;
            $user_cd =  $this->getCurrentUser();
            $user =  $this->getInfoCurrentUser($user_cd, $user_login);
            $org = $this->organization_service->getMainOrganizationsByUser($user_cd);
            //CR No.37: 面談、社内予定、説明会は不可です。講演会は可能です。
            $conditions['start_date'] = $request->start_date;
            $conditions['end_date'] = $request->start_date;
            $conditions['user_cd'] = $user_cd;
            $conditions['schedule_type'] = $request->schedule_type;
            $report_status = $this->service->reportStatusListAdd($conditions);
            if (!$report_status) {
                return  $this->responseErrorValidate(__('schedule.uncreate'));
            }
            $plan_flag = 0;
            $act_flag = 0;
            if ($request->start_date > $current) {
                $plan_flag = 1;
            }
            $display_option_type = '';
            if ($plan_flag == 0 && $act_flag == 0) {
                $display_option_type = 'C00';
            } else if ($plan_flag == 1 && $act_flag == 0) {
                $display_option_type = 'C01';
            } else if ($plan_flag == 0 && $act_flag == 1) {
                $display_option_type = 'C02';
            } else {
                $display_option_type = 'C03';
            }
            //Insert t_schedule table
            $schedule['schedule_type'] = $request->schedule_type;
            $schedule['start_date'] = $request->start_date;
            $schedule['start_time'] = $request->start_time;
            $schedule['end_time'] = $request->end_time;
            $schedule['all_day_flag'] = $request->all_day_flag;
            $schedule['display_option_type'] = $display_option_type;
            $schedule['user_cd'] = $user_cd;
            $schedule['title'] = $request->title;
            $this->service->addSchedule($schedule);
            //Insert t_visit table
            $last_index_schedule = $this->service->lastInsertedSchedule();
            $visit['schedule_id'] = $last_index_schedule->schedule_id;
            $visit['important_flag'] = 0;
            $visit['facility_cd'] = $request->facility_cd;
            $visit['facility_short_name'] = $request->facility_short_name;
            $visit['user_cd'] = $user_cd;
            $visit['user_name'] = $user->user_name;
            $visit['org_cd'] = $org->org_cd ?? "";
            $visit['org_short_name'] = $org->org_short_name ?? "";
            $visit['remarks'] = "";
            $this->service->addVisit($visit);
            $last_index_visit = $this->service->lastInsertedVisit();
            //Insert t_call table
            $call['visit_id'] = $last_index_visit->visit_id;
            $call['person_cd'] = $request->person_cd;
            $call['plan_flag'] = $plan_flag;
            $call['act_flag'] = $act_flag;
            $call['person_name'] = $request->person_name;
            $call['department_cd'] = $request->department_cd;
            $call['department_name'] = $request->department_name;
            $call['display_position_name'] = $request->display_position_name;
            $call['off_label_flag'] = 0;
            $call['off_label_concent_flag'] = 0;
            $call['off_label_call_time'] = null;
            $call['related_product'] = null;
            $call['question'] = null;
            $call['answer'] = null;
            $call['re_question'] = null;
            $call['literature'] = null;
            $this->service->addCall($call);
            DB::commit();
            return $this->responseCreated(__('schedule.create_successfully'));
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/schedule/search-stock",
     *      operationId="getStockList",
     *      tags={"A01-S01"},
     *      summary="Get stock list.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="status_type",
     *                      type="string",
     *                      description="status type value 0 or 1",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {{
     *                      "stock_id": 1893,
     *                      "document_list": "null",
     *                      "product_list": "[{\'product_cd\': \'BBB\', \'product_name\': \'BBB製品\'},{\'product_cd\': \'KKK\', \'product_name\': \'KKK製品\'}]",
     *                      "facility_cd": "011010014",
     *                      "facility_category_type": "04",
     *                      "facility_short_name": "武井内科病院（札幌市中央区）",
     *                      "display_position_cd": "501",
     *                      "department_cd": "9999",
     *                      "department_name": "その他",
     *                      "person_cd": "01101156",
     *                      "person_name": "村松 春実",
     *                      "content_cd": "70",
     *                      "content_name": "処方症例確認",
     *                      "status_type": "1",
     *                      "stock_type": "10",
     *                      "action_id": "null",
     *                      "stock_date": "2021-12-26",
     *                      "position_name": "役職なし"
     *                  }}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getStockList(SearchStockRequest $request)
    {
        $user_cd =  $this->getCurrentUser();
        $conditions['user_cd'] = $user_cd;
        $conditions['status_type'] = $request->status_type == 1 ? STOCK_STATUS_UNPLANNED : STOCK_STATUS_PLANNED;
        $conditions['limit'] = $request->filled('limit') ? $request->limit : 100;
        $conditions['offset'] = $request->filled('offset') ? $request->offset : 0;
        $result = $this->service->getStockList($conditions);
        return $this->responseSuccess('success', $result);
    }

    /**
     * @OA\POST(
     *      path="/api/schedule/add-facility-person-select",
     *      operationId="addFacilityPersonSelect",
     *      tags={"A01-S01"},
     *      summary="Add facility person select.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_type",
     *                      type="string",
     *                      description="schedule type",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="string",
     *                      description="start date",
     *                      default="2021-12-27"
     *                  ),
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="string",
     *                      description="start time",
     *                      default="2021-12-27 17:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="string",
     *                      description="end time",
     *                      default="2021-12-27 18:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      description="title",
     *                      default="足立クリニック（厚岸町）"
     *                  ),
     *                  @OA\Property(
     *                      property="all_day_flag",
     *                      type="string",
     *                      description="all day flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="display_option_type",
     *                      type="string",
     *                      description="display option type",
     *                      default="V99"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="facility cd",
     *                      default="011010018"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      description="facility short name",
     *                      default="藤川医院　医療（札幌市中央区）"
     *                  ),
     *                  @OA\Property(
     *                      property="plan_flag",
     *                      type="string",
     *                      description="plan flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="act_flag",
     *                      type="string",
     *                      description="act flag",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      description="person cd",
     *                      default="01101159"
     *                  ),
     *                  @OA\Property(
     *                      property="person_name",
     *                      type="string",
     *                      description="person name",
     *                      default="倉田 君吉"
     *                  ),
     *                  @OA\Property(
     *                      property="department_cd",
     *                      type="string",
     *                      description="department cd",
     *                      default="0641"
     *                  ),
     *                  @OA\Property(
     *                      property="department_name",
     *                      type="string",
     *                      description="department name",
     *                      default="糖尿病・内分泌・代謝内科"
     *                  ),
     *                  @OA\Property(
     *                      property="display_position_name",
     *                      type="string",
     *                      description="display position name",
     *                      default="役職なし"
     *                  ),
     *                  @OA\Property(
     *                      property="content_cd",
     *                      type="string",
     *                      description="content cd",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="content_name_tran",
     *                      type="string",
     *                      description="content name tran",
     *                      default="情報提供"
     *                  ),
     *                  @OA\Property(
     *                      property="product_list",
     *                      type="string",
     *                      description="product list",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="document_list",
     *                      type="string",
     *                      description="document list",
     *                      default="[]"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "201",
     *                  "message": "success",
     *                  "data": {}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function addStock(AddFacilityPersonSelectRequest $request)
    {
        DB::beginTransaction();
        $current = $this->currentJapanDateTime('Y-m-d');
        $user_login = IS_USER;
        $user_cd =  $this->getCurrentUser();
        $user =  $this->getInfoCurrentUser($user_cd, $user_login);
        $org = $this->organization_service->getMainOrganizationsByUser($user_cd);
        //CR No.37: 面談、社内予定、説明会は不可です。講演会は可能です。
        $conditions['start_date'] = $request->start_date;
        $conditions['end_date'] = $request->start_date;
        $conditions['user_cd'] = $user_cd;
        $conditions['schedule_type'] = $request->schedule_type;
        $report_status = $this->service->reportStatusListAdd($conditions);
        if (!$report_status) {
            return  $this->responseErrorValidate(__('schedule.uncreate'));
        }
        $product_list = empty($request->product_list) ? [] : $request->product_list;
        $document_list = empty($request->document_list) ? [] : $request->document_list;
        $plan_flag = 0;
        $act_flag = 0;
        if ($request->start_date > $current) {
            $plan_flag = 1;
        }
        $display_option_type = '';
        if ($plan_flag == 0 && $act_flag == 0) {
            $display_option_type = 'C00';
        } else if ($plan_flag == 1 && $act_flag == 0) {
            $display_option_type = 'C01';
        } else if ($plan_flag == 0 && $act_flag == 1) {
            $display_option_type = 'C02';
        } else {
            $display_option_type = 'C03';
        }
        //Insert t_schedule table
        $schedule['schedule_type'] = $request->schedule_type;
        $schedule['start_date'] = $request->start_date;
        $schedule['start_time'] = $request->start_time;
        $schedule['end_time'] = $request->end_time;
        $schedule['all_day_flag'] = $request->all_day_flag;
        $schedule['display_option_type'] = $display_option_type;
        $schedule['user_cd'] = $user_cd;
        $schedule['title'] = $request->title;
        $result = $this->service->addSchedule($schedule);
        if (!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('schedule.system_error'));
        }
        //Insert t_visit table
        $last_index_schedule = $this->service->lastInsertedSchedule();
        $visit['schedule_id'] = $last_index_schedule->schedule_id;
        $visit['important_flag'] = 0;
        $visit['facility_cd'] = $request->facility_cd;
        $visit['facility_short_name'] = $request->facility_short_name;
        $visit['user_cd'] = $user_cd;
        $visit['user_name'] = $user->user_name;
        $visit['org_cd'] = $org->org_cd ?? "";
        $visit['org_short_name'] = $org->org_short_name ?? "";
        $visit['remarks'] = "";
        $result = $this->service->addVisit($visit);
        if (!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('schedule.system_error'));
        }
        $last_index_visit = $this->service->lastInsertedVisit();
        //Insert t_call table
        $call['visit_id'] = $last_index_visit->visit_id;
        $call['person_cd'] = $request->person_cd;
        $call['plan_flag'] = $plan_flag;
        $call['act_flag'] = $act_flag;
        $call['person_name'] = $request->person_name;
        $call['department_cd'] = $request->department_cd;
        $call['department_name'] = $request->department_name;
        $call['display_position_name'] = $request->display_position_name;
        $call['off_label_flag'] = 0;
        $call['off_label_concent_flag'] = 0;
        $call['off_label_call_time'] = null;
        $call['related_product'] = null;
        $call['question'] = null;
        $call['answer'] = null;
        $call['re_question'] = null;
        $call['literature'] = null;
        $result = $this->service->addCall($call);
        if (!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('schedule.system_error'));
        }
        //Insert t_detail, t_detail_document table
        $last_index_call = $this->service->lastInsertedCall();
        //面談内容・品目・資材が設定されていないストックを登録する際はディテールは登録せず、スケジュール・訪問・面談テーブルのデータのみ登録してください。
        if (!empty($product_list) && !empty($request->content_cd)) {
            foreach ($product_list as $key => $product) {
                //Insert t_detail table
                $detail['call_id'] = $last_index_call->call_id;
                $detail['detail_order'] = $key + 1;
                $detail['content_cd'] = $request->content_cd;
                $detail['content_name_tran'] = $request->content_name_tran;
                $detail['product_cd'] = $product['product_cd'];
                $detail['product_name'] = $product['product_name'];
                $result = $this->service->addDetail($detail);
                if (!$result) {
                    DB::rollBack();
                    return $this->responseSystemError(__('schedule.system_error'));
                }
                $last_index_detail = $this->service->lastInsertedDetail();
                $documents = [];
                foreach ($document_list as $document) {
                    array_push($documents, [
                        'detail_id' => $last_index_detail->detail_id,
                        'document_id' => $document['document_id']
                    ]);
                }
                if (!empty($documents)) {
                    $result = $this->service->addDetailDocument($documents);
                    if (!$result) {
                        DB::rollBack();
                        return $this->responseSystemError(__('schedule.system_error'));
                    }
                }
                break;
            }
        }
        DB::commit();
        return $this->responseCreated(__('schedule.create_successfully'));
    }

    /**
     * @OA\GET(
     *      path="/api/schedule/select-facility-person-group",
     *      operationId="getFacilityPersonGroup",
     *      tags={"A01-S01"},
     *      summary="Get facility or person group.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="select",
     *                      type="string",
     *                      description="value is f or p",
     *                      default="f"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {{
     *                      "select_person_group_id": 1,
     *                      "user_cd": "10001",
     *                      "select_person_group_name": "Group A"
     *                  }}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function selectFacilityPersonGroup(Request $request)
    {
        try {
            $user_cd =  $this->getCurrentUser();
            $select = $request->select == 'f' ? 'f' : 'p';
            if ($select == 'f') {
                $result = $this->service->selectFacilityGroup($user_cd);
            } else {
                $result = $this->service->selectPersonGroup($user_cd);
            }
            return $this->responseSuccess('success', $result);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/schedule/get-facility-group",
     *      operationId="getFacilityGroup",
     *      tags={"A01-S01"},
     *      summary="Get facility group.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="select_facility_group_id",
     *                      type="string",
     *                      default="67"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {{
     *                      "facility_cd": "011080005",
     *                      "facility_short_name": "村上大学医学部附属病院（札幌市厚別区）"
     *                  }}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getFacilityGroup(Request $request)
    {
        try {
            $conditions['limit'] = $request->filled('limit') ? $request->limit : 100;
            $conditions['offset'] = $request->filled('offset') ? $request->offset : 0;
            $conditions['select_facility_group_id'] = $request->select_facility_group_id ?? "";
            $result = $this->service->getFacilityGroupList($conditions);
            return $this->responseSuccess('success', $result);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/schedule/get-person-group",
     *      operationId="getPersonGroup",
     *      tags={"A01-S01"},
     *      summary="Get person group.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="select_person_group_id",
     *                      type="string",
     *                      default="67"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {{
     *                      "facility_cd": "011010005",
     *                      "facility_short_name": "村上大学医学部附属病院（札幌市中央区）",
     *                      "facility_category_type": "01",
     *                      "person_cd": "01101050",
     *                      "person_name": "山内 雅則",
     *                      "department_cd": "3704",
     *                      "department_name": "輸血部",
     *                      "display_position_cd": "144",
     *                      "position_name": "教授",
     *                      "content_list": "",
     *                      "product_list": "",
     *                      "document_list": ""
     *                  }}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getPersonGroup(Request $request)
    {
        try {
            $conditions['limit'] = $request->filled('limit') ? $request->limit : 100;
            $conditions['offset'] = $request->filled('offset') ? $request->offset : 0;
            $conditions['select_person_group_id'] = $request->select_person_group_id ?? "";
            $result = $this->service->getPersonGroupList($conditions);
            return $this->responseSuccess('success', $result);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/schedule/add-facility-select",
     *      operationId="addFacilitySelect",
     *      tags={"A01-S01"},
     *      summary="Add facility select.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_type",
     *                      type="string",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="string",
     *                      default="2021-12-27"
     *                  ),
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="string",
     *                      default="2021-12-27 17:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="string",
     *                      default="2021-12-27 18:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      default="足立クリニック（厚岸町）"
     *                  ),
     *                  @OA\Property(
     *                      property="all_day_flag",
     *                      type="string",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="display_option_type",
     *                      type="string",
     *                      default="V99"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      default="011010018"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      default="藤川医院　医療（札幌市中央区）"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "201",
     *                  "message": "success",
     *                  "data": {}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function addFacilitySelect(AddFacilitySelectRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_login = IS_USER;
            $user_cd =  $this->getCurrentUser();
            $user =  $this->getInfoCurrentUser($user_cd, $user_login);
            $org = $this->organization_service->getMainOrganizationsByUser($user_cd);
            //CR No.37: 面談、社内予定、説明会は不可です。講演会は可能です。
            $conditions['start_date'] = $request->start_date;
            $conditions['end_date'] = $request->start_date;
            $conditions['user_cd'] = $user_cd;
            $conditions['schedule_type'] = $request->schedule_type;
            $report_status = $this->service->reportStatusListAdd($conditions);
            if (!$report_status) {
                return  $this->responseErrorValidate(__('schedule.uncreate'));
            }

            //Insert t_schedule table
            $schedule['schedule_type'] = $request->schedule_type;
            $schedule['start_date'] = $request->start_date;
            $schedule['start_time'] = $request->start_time;
            $schedule['end_time'] = $request->end_time;
            $schedule['title'] = $request->title;
            $schedule['all_day_flag'] = $request->all_day_flag;
            $schedule['display_option_type'] = $request->display_option_type;
            $schedule['user_cd'] = $user_cd;
            $this->service->addSchedule($schedule);
            //Insert t_visit table
            $last_index_schedule = $this->service->lastInsertedSchedule();
            $visit['schedule_id'] = $last_index_schedule->schedule_id;
            $visit['important_flag'] = 0;
            $visit['facility_cd'] = $request->facility_cd;
            $visit['facility_short_name'] = $request->facility_short_name;
            $visit['user_cd'] = $user_cd;
            $visit['user_name'] = $user->user_name;
            $visit['org_cd'] = $org->org_cd ?? "";
            $visit['org_short_name'] = $org->org_short_name ?? "";
            $visit['remarks'] = "";
            $this->service->addVisit($visit);
            DB::commit();
            return $this->responseCreated(__('schedule.create_successfully'));
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/schedule/add-other-than-interview",
     *      operationId="addOtherThanInterview",
     *      tags={"A01-S01"},
     *      summary="Addition of events other than interview.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_type",
     *                      type="string",
     *                      description="schedule type",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="string",
     *                      description="start date",
     *                      default="2021-12-27"
     *                  ),
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="string",
     *                      description="start time",
     *                      default="2021-12-27 17:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="string",
     *                      description="end time",
     *                      default="2021-12-27 18:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="all_day_flag",
     *                      type="string",
     *                      description="all day flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      description="title",
     *                      default="社内予定"
     *                  ),
     *                  @OA\Property(
     *                      property="display_option_type",
     *                      type="string",
     *                      description="display option type",
     *                      default="O99"
     *                  ),
     *                  @OA\Property(
     *                      property="office_schedule_type",
     *                      type="string",
     *                      description="office schedule type",
     *                      default="40"
     *                  ),
     *                  @OA\Property(
     *                      property="office_title",
     *                      type="string",
     *                      description="office title",
     *                      default="社内予定"
     *                  ),
     *                  @OA\Property(
     *                      property="private_title",
     *                      type="string",
     *                      description="private title",
     *                      default="社内予定"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "201",
     *                  "message": "success",
     *                  "data": {}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function addOtherThanInterview(AddOtherThanInterviewRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_cd =  $this->getCurrentUser();
            //CR No.37: 面談、社内予定、説明会は不可です。講演会は可能です。
            $conditions['start_date'] = $request->start_date;
            $conditions['end_date'] = $request->start_date;
            $conditions['user_cd'] = $user_cd;
            $conditions['schedule_type'] = $request->schedule_type;
            $report_status = $this->service->reportStatusListAdd($conditions);
            if (!$report_status) {
                return  $this->responseErrorValidate(__('schedule.uncreate'));
            }
            //Insert t_schedule table
            $schedule['schedule_type'] = $request->schedule_type;
            $schedule['start_date'] = $request->start_date;
            $schedule['start_time'] = $request->start_time;
            $schedule['end_time'] = $request->end_time;
            $schedule['title'] = $request->title ?? "";
            $schedule['all_day_flag'] = $request->all_day_flag;
            $schedule['display_option_type'] = $request->display_option_type;
            $schedule['user_cd'] = $user_cd;
            $this->service->addSchedule($schedule);
            $last_index_schedule = $this->service->lastInsertedSchedule();
            if (isset($request->private_title)) { //If add new private
                //Insert t_private table
                $private['schedule_id'] = $last_index_schedule->schedule_id;
                $private['title'] = $request->private_title ?? null;
                $this->service->addPrivate($private);
            } else { //If add new internal plan
                //Insert t_office_schedule table
                $office_schedule['schedule_id'] = $last_index_schedule->schedule_id;
                $office_schedule['office_schedule_type'] = $request->office_schedule_type ?? null;
                $office_schedule['title'] = $request->office_title ?? null;
                $this->service->addOfficeSchedule($office_schedule);
            }
            DB::commit();
            return $this->responseCreated(__('schedule.create_successfully'));
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }

    /**
     * @OA\PUT(
     *      path="/api/schedule/update-schedule",
     *      operationId="updateSchedule",
     *      tags={"A01-S01"},
     *      summary="Update schedule.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="schedule id",
     *                      default="43"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="string",
     *                      description="start date",
     *                      default="2021-12-27"
     *                  ),
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="string",
     *                      description="start time",
     *                      default="2021-12-27 17:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="string",
     *                      description="end time",
     *                      default="2021-12-27 18:21:00"
     *                  ),
     *                  @OA\Property(
     *                      property="all_day_flag",
     *                      type="string",
     *                      description="all day flag",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "201",
     *                  "message": "success",
     *                  "data": {}
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function updateSchedule(UpdateScheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            $dateSystem = date_create($this->system->getCurrentSystemDateTime())->format('Y/m/d');
            $start_date = date("Y/m/d", strtotime($request->start_date));
            $schedule_type = $request->schedule_type;
            $schedule['schedule_id'] = $request->schedule_id;
            $schedule['start_date'] = $request->start_date;
            $schedule['start_time'] = $request->start_time;
            $schedule['end_time'] = $request->end_time;
            $schedule['all_day_flag'] = $request->all_day_flag;
            $report_status = $this->service->reportStatus($schedule['schedule_id'], $schedule['start_date']);
            $briefing_stauts = $this->service->getBriefingStatusFromSchedule($schedule['schedule_id'])->status_type ?? 0;
            $convention_stauts = $this->service->getConventionStatusFromSchedule($schedule['schedule_id'])->status_type ?? 0;
            $check_usage_call = $this->service->checkCallUsageInSchedule($schedule['schedule_id'])->record > 0 ? true : false;
            //C01-S02: 過去日には登録できません。
            if (($dateSystem >= $start_date) && $schedule_type == SCHEDULE_DIVISION_CONVENTION && in_array($convention_stauts, [CONVENTION_STATUS_ENTERING_A_PLAN, CONVENTION_STATUS_WAITING_FOR_PLAN_APPROVAL])) {
                return $this->responseErrorForbidden("過去日には登録できません。");
            }
            //CR No.37: 面談、社内予定、説明会は不可です。講演会は可能です。
            $user_cd =  $this->getCurrentUser();
            $schedule_info = $this->service->getScheduleById($schedule['schedule_id']);
            $conditions['start_date'] = $request->start_date;
            $conditions['end_date'] = $request->start_date;
            $conditions['user_cd'] = $user_cd;
            $conditions['schedule_type'] = $schedule_type;
            $conditions['schedule_start_date_old'] = $schedule_info->start_date;
            $conditions['schedule_end_date_old'] = $schedule_info->start_date;
            $report_status_ = $this->service->reportStatusListEdit($conditions);
            if (!$report_status_) {
                return  $this->responseErrorValidate(__('schedule.unedit', ['0' => REPORT_STATUS_NAME_SUBMITTED, '1' => REPORT_STATUS_NAME_APPROVED]));
            }
            //CR No.37: 面談、社内予定、説明会は不可です。講演会は可能です。
            if ($schedule_type == SCHEDULE_DIVISION_CALL && in_array($report_status, [REPORT_STATUS_VALUE_SUBMITTED, REPORT_STATUS_VALUE_APPROVED])) { //日報を提出した時点で編集不可。また、日報が差し戻されても、ナレッジの一部として使用されているデータ（面談単位）は編集不可。
                return  $this->responseErrorValidate(__('schedule.uncreate', ['0' => REPORT_STATUS_NAME_SUBMITTED, '1' => REPORT_STATUS_NAME_APPROVED]));
            } else if ($schedule_type == SCHEDULE_DIVISION_CALL && $check_usage_call) {
                return  $this->responseErrorValidate(__('schedule.call_usage_in_knowledge'));
            } else if ($schedule_type == SCHEDULE_DIVISION_IN_HOUSE_SCHEDULE && in_array($report_status, [REPORT_STATUS_VALUE_SUBMITTED, REPORT_STATUS_VALUE_APPROVED])) { //日報を提出した時点で編集不可。
                return  $this->responseErrorValidate(__('schedule.uncreate', ['0' => REPORT_STATUS_NAME_SUBMITTED, '1' => REPORT_STATUS_NAME_APPROVED]));
            } else if ($schedule_type == SCHEDULE_DIVISION_BRIEFING && in_array($report_status, [REPORT_STATUS_VALUE_SUBMITTED, REPORT_STATUS_VALUE_APPROVED])) { //説明会結果の承認申請をした時点で編集不可。また、説明会結果の承認が差し戻されても、日報の一部として提出されている場合、日付の変更は不可（時間や説明会内容は編集可）
                return  $this->responseErrorValidate(__('schedule.uncreate', ['0' => REPORT_STATUS_NAME_SUBMITTED, '1' => REPORT_STATUS_NAME_APPROVED]));
            } else if ($schedule_type == SCHEDULE_DIVISION_BRIEFING && in_array($briefing_stauts, [BRIEFING_STATUS_WAITING_FOR_RESULT_APPROVAL, BRIEFING_STATUS_RESULT_APPROVED])) { //説明会結果の承認申請をした時点で編集不可。また、説明会結果の承認が差し戻されても、日報の一部として提出されている場合、日付の変更は不可（時間や説明会内容は編集可）
                return  $this->responseErrorValidate(__('schedule.bri_status'));
            } else if ($schedule_type == SCHEDULE_DIVISION_CONVENTION && in_array($report_status, [REPORT_STATUS_VALUE_SUBMITTED, REPORT_STATUS_VALUE_APPROVED])) { //講演会結果の承認申請をした時点で編集不可。また、講演会結果の承認が差し戻されても、講演会の「対象組織」に所属する他ユーザを含め、誰かの日報の一部として提出されている場合、日付の変更は不可（時間や講演会内容は編集可）
                return  $this->responseErrorValidate(__('schedule.uncreate', ['0' => REPORT_STATUS_NAME_SUBMITTED, '1' => REPORT_STATUS_NAME_APPROVED]));
            } else if ($schedule_type == SCHEDULE_DIVISION_CONVENTION && in_array($convention_stauts, [CONVENTION_STATUS_WAITING_FOR_RESULT_APPROVAL, CONVENTION_STATUS_RESULT_APPROVED])) { //講演会結果の承認申請をした時点で編集不可。また、講演会結果の承認が差し戻されても、講演会の「対象組織」に所属する他ユーザを含め、誰かの日報の一部として提出されている場合、日付の変更は不可（時間や講演会内容は編集可）
                return  $this->responseErrorValidate(__('schedule.con_status'));
            }
            $this->service->updateSchedule($schedule);
            DB::commit();
            return $this->responseCreated(__('schedule.update_successfully'));
        } catch (Exception $ex) {
            DB::rollBack();
            return $this->responseSystemError(__('schedule.system_error'));
        }
    }
}
