<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\BriefingSearchService;
use App\Http\Requests\Api\BriefingSearch\BriefingIDRequest;
use App\Http\Requests\Api\BriefingSearch\BriefingDetailRequest;
use App\Repositories\DailyReport\DailyReportRepository;
use App\Repositories\BriefingSearch\BriefingSearchRepositoryInterface;
use App\Services\AuthService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Api\BriefingSearch\ApprovalRequest;
use App\Http\Requests\Api\BriefingSearch\ListBriefingRequest;
use App\Http\Requests\Api\BriefingSearch\CancelSubmitRequest;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Services\SystemParameterService;
use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;
use App\Repositories\VariableDefinition\VariableDefinitionRepositoryInterface;
use App\Repositories\Auth\AuthRepositoryInterface;

class BriefingSearchController extends Controller
{
    private $service, $submit_plan, $save_result, $pending_briefing, $request_type_plan, $request_type_result, $save_plan, $repo_briefing;
    private $system, $schedule, $convention, $variable, $definition_label_briefing_result, $definition_label_briefing_usage_type, $auth;
    private $submit_result, $submit_finnal_plan, $authService;
    public function __construct(
        BriefingSearchService $service,
        SystemParameterService $system,
        ScheduleRepositoryInterface $schedule,
        ConventionSearchRepositoryInterface $convention,
        VariableDefinitionRepositoryInterface $variable,
        BriefingSearchRepositoryInterface $repo_briefing,
        AuthRepositoryInterface $auth,
        AuthService $authService
    ) {
        $this->auth = $auth;
        $this->schedule = $schedule;
        $this->repo_briefing = $repo_briefing;
        $this->system = $system;
        $this->service = $service;
        $this->variable = $variable;
        $this->convention = $convention;
        $this->save_plan = 10;
        $this->submit_plan = 20;
        $this->submit_finnal_plan = 30;
        $this->save_result = 40;
        $this->submit_result = 50;
        $this->pending_briefing = 70;
        $this->request_type_plan = 1;
        $this->request_type_result = 2;
        $this->authService = $authService;
        $this->definition_label_briefing_result = '説明会結果';
        $this->definition_label_briefing_usage_type = '説明会';
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/briefing-search",
     *      operationId="getIndexBriefing",
     *      tags={"B01-S01"},
     *      summary="get Index Briefing",
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
    public function getIndexBriefing()
    {
        $data = $this->service->getDataIndex();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/briefing-search/list-data",
     *      operationId="getListDataBriefing",
     *      tags={"B01-S01"},
     *      summary="get List Data Briefing",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="start_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="end_date",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="briefing_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="remand_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="briefing_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="approval_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="product_cd",in="query", @OA\Schema(type="string")),
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
    public function getListDataBriefing(ListBriefingRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $data = $this->service->getListDataBriefing($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/briefing-search/index-detail",
     *      operationId="getDataIndexDetailBriefing",
     *      tags={"B01-S02"},
     *      summary="get Data Index Detail Briefing",
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
    public function getDataIndexDetailBriefing()
    {
        $data = $this->service->getDataIndexDetailBriefing();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/briefing-search/detail",
     *      operationId="getDataDetailBriefing",
     *      tags={"B01-S02"},
     *      summary="get Data Detail Briefing",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="briefing_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="string")),
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
    public function getDataDetailBriefing(BriefingDetailRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getDataDetailBriefing($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\POST(
     *      path="/api/briefing-search/save-detail",
     *      operationId="saveDetailBriefing",
     *      tags={"B01-S02"},
     *      summary="save Detail Briefing",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="briefing_id",in="query", @OA\Schema(type="string")),
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
    public function saveDetailBriefing(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!$this->checkDeadlineConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が説明会入力期限外（" . $this->end_date . "）です。");
            }
            $request->user_login = $this->getCurrentUser();
            $request->status_type = $request->status_type >= 30 ? $this->save_result : $this->save_plan;
            $request->implement_user_post = $this->auth->getInfoUser($request->user_login)->implement_user_post;
            $request->implement_user_post_name = $this->auth->getInfoUser($request->user_login)->implement_user_post_name;
            $detail_user = $this->authService->getInfoUser($request->user_login);
            $request->user_name = $detail_user->user_name;
            $request->org_cd = $detail_user->org_cd;
            $request->org_label = $detail_user->org_label;
            $data = $this->service->saveDetailBriefing($request);
            if ($request->request_type == $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_briefing_result)->definition_value) {
                $document_review = $this->convention->getDocumentReview($request->briefing_id, $this->convention->getDocumentUsageType($this->definition_label_briefing_usage_type));
                $data['document_review'] = count($document_review) > 0 ? 0 : 1;
            }
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
     *      path="/api/briefing-search/submit",
     *      operationId="submmitPlanBriefing",
     *      tags={"B01-S02"},
     *      summary="submit Plan Briefing",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="briefing_id",in="query", @OA\Schema(type="string")),
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
    public function submitBriefing(Request $request)
    {
        DB::beginTransaction();
        try {
            set_time_limit(0);
            $briefing_detail = $this->repo_briefing->getDataDetailBriefingByBriefingID($request->briefing_id, $request->schedule_id);
            if (!$this->checkDeadlineConvention($request->start_date)) {
                return $this->responseErrorForbidden($request->start_date . "の開催日が説明会入力期限外（" . $this->end_date . "）です。");
            }
            if (isset($briefing_detail->schedule_id)) {
                if ($this->checkStatusReport($briefing_detail->schedule_id, $briefing_detail->start_date)) {
                    if (date_create($briefing_detail->start_date)->format('Y-m-d') != date_create($request->start_date)->format('Y-m-d')) {
                        return $this->responseErrorForbidden("日報が提出済のため、日付は変更できません。日報を差戻ししてから変更してください。");
                    }
                }
            }
            if (is_object($briefing_detail)) {
                if (!($briefing_detail->status_type != $this->save_plan || $briefing_detail->status_type != $this->submit_plan)) {
                    return $this->responseErrorForbidden("本説明会は承認されましたので提出はできません。");
                }
            }
            $request->user_login = $this->getCurrentUser();
            $detail_user = $this->authService->getInfoUser($request->user_login);
            $request->user_name = $detail_user->user_name;
            $request->org_cd = $detail_user->org_cd;
            $request->org_label = $detail_user->org_label;
            $user_approval_briefing = $this->service->getUserApproval($request->user_login);
            if(empty($user_approval_briefing)){
                return $this->responseErrorValidate(__('briefing_search.no_approval'));
            }
            $data = $this->service->submitBriefing($request);
            if ($request->request_type == $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_briefing_result)->definition_value) {
                $document_review = $this->convention->getDocumentReview($request->briefing_id, $this->convention->getDocumentUsageType($this->definition_label_briefing_usage_type));
                $data['document_review'] = count($document_review) > 0 ? 0 : 1;
            }
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
     *      path="/api/briefing-search/approval-plan",
     *      operationId="approvalPlanBriefing",
     *      tags={"B01-S02"},
     *      summary="approval plam Briefing",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="briefing_id",in="query", @OA\Schema(type="string")),
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
    public function approvalBriefing(ApprovalRequest $request)
    {
        DB::beginTransaction();
        try {
            $briefing_detail = $this->repo_briefing->getDataDetailBriefingByBriefingID($request->briefing_id, $request->schedule_id);
            if (!$this->checkDeadlineConvention($briefing_detail->start_date)) {
                return $this->responseErrorForbidden($briefing_detail->start_date . "の開催日が説明会入力期限外（" . $this->end_date . "）です。");
            }
            if (!($briefing_detail->status_type == $this->submit_plan || $briefing_detail->status_type == $this->submit_result)) {
                return $this->responseErrorForbidden("本説明会は承認されましたので承認はできません。");
            }
            $request->briefing_detail = $briefing_detail;
            $request->user_login = $this->getCurrentUser();
            $data = $this->service->approvalBriefing($request);
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
     *      path="/api/briefing-search/remand-plan",
     *      operationId="remandBriefing",
     *      tags={"B01-S02"},
     *      summary="remand Briefing",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="briefing_id",in="query", @OA\Schema(type="string")),
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
    public function remandBriefing(Request $request)
    {
        DB::beginTransaction();
        try {
            $briefing_detail = $this->repo_briefing->getDataDetailBriefingByBriefingID($request->briefing_id, $request->schedule_id);
            if (!$this->checkDeadlineConvention($briefing_detail->start_date)) {
                return $this->responseErrorForbidden($briefing_detail->start_date . "の開催日が説明会入力期限外（" . $this->end_date . "）です。");
            }
            $request->briefing_detail = $briefing_detail;
            $request->user_login = $this->getCurrentUser();
            $data = $this->service->remandBriefing($request);
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\PUT(
     *      path="/api/briefing-search/cancel-submit",
     *      operationId="cancelSubmitBriefing",
     *      tags={"B01-S02"},
     *      summary="cancel Submit Briefing",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="briefing_id",in="query", @OA\Schema(type="string")),
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
    public function cancelSubmitBriefing(CancelSubmitRequest $request)
    {
        DB::beginTransaction();
        try {
            $briefing_detail = $this->repo_briefing->getDataDetailBriefingByBriefingID($request->briefing_id, $request->schedule_id);
            if (!($briefing_detail->status_type == $this->submit_plan || $briefing_detail->status_type == $this->submit_result)) {
                return $this->responseErrorForbidden("本説明会は承認されましたので提出取消しはできません。");
            }
            $this->service->updateStatusTypeBriefing($request);
            DB::commit();
            return $this->responseCreated('success', []);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\DELETE(
     *      path="/api/briefing-search/delete-detail",
     *      operationId="deleteDetailBriefing",
     *      tags={"B01-S02"},
     *      summary="delete Submit Briefing",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="briefing_id",in="query", @OA\Schema(type="string")),
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
    public function deleteDetailBriefing(BriefingIDRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->service->deleteDetailBriefing($request);
            DB::commit();
            return $this->responseNoContent('success');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\PUT(
     *      path="/api/briefing-search/pending",
     *      operationId="pendingBriefing",
     *      tags={"B01-S02"},
     *      summary="pending Briefing",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="briefing_id",in="query", @OA\Schema(type="string")),
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
    public function pendingBriefing(BriefingIDRequest $request)
    {
        DB::beginTransaction();
        try {
            $briefing_detail = $this->repo_briefing->getDataDetailBriefingByBriefingID($request->briefing_id, $request->schedule_id);
            if (!$this->checkDeadlineConvention($briefing_detail->start_date)) {
                return $this->responseErrorForbidden($briefing_detail->start_date . "の開催日が説明会入力期限外（" . $this->end_date . "）です。");
            }
            $request->briefing_detail = $briefing_detail;
            $request->status_type = $this->pending_briefing;
            $this->service->pendingBriefing($request);
            DB::commit();
            return $this->responseNoContent('success');
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

    public function checkDeadlineConvention($date_input)
    {
        $dateSystem = date_create($this->system->getCurrentSystemDateTime())->format('Y/m/d');
        $input_deadline_convention = $this->convention->getInputDeadlineConvention($this->definition_label_briefing_usage_type);
        $this->end_date = $dateSystem;
        if (isset($input_deadline_convention->parameter_value)) {
            $this->end_date = date("Y/m/d", strtotime("-" . $input_deadline_convention->parameter_value . " months", strtotime($dateSystem)));
        }
        if ($this->end_date >= date_create($date_input)->format('Y/m/d')) {
            return false;
        }
        return true;
    }
}
