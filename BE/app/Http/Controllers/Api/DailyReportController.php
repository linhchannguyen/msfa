<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DailyReportService;
use App\Services\SystemParameterService;
use App\Traits\DateTimeTrait;
use Illuminate\Http\Request;
use App\Http\Requests\Api\DailyReport\DailyReportRequest;
use App\Http\Requests\Api\DailyReport\ReportDetailRequest;
use App\Http\Requests\Api\DailyReport\VacationReportRequest;
use App\Http\Requests\Api\DailyReport\SaveReportRequest;
use App\Http\Requests\Api\DailyReport\ApprovalReportRequest;
use App\Http\Requests\Api\DailyReport\ReportRequest;
use App\Traits\StatusReportTrait;
use App\Services\AuthService;
use App\Repositories\DailyReport\DailyReportRepositoryInterface;
use App\Repositories\VariableDefinition\VariableDefinitionRepositoryInterface;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyReportController extends Controller
{
    use DateTimeTrait, StatusReportTrait;
    private $service, $serviceDate, $date, $authService, $repo, $variable, $definition_label_report;

    public function __construct(
        DailyReportService $service,
        SystemParameterService $serviceDate,
        AuthService $authService,
        DailyReportRepositoryInterface $repo,
        VariableDefinitionRepositoryInterface $variable
    ) {
        $this->service = $service;
        $this->repo = $repo;
        $this->variable = $variable;
        $this->authService = $authService;
        $this->serviceDate = $serviceDate;
        $this->definition_label_report = '報告';
        $this->date = $this->serviceDate->getCurrentSystemDateTime();
        $this->middleware('role.has:' . config('role.CALL_IMPLEMENTER.CODE') . ',' . config('role.SYS_MG.CODE'));
    }
    /**
     * @OA\GET(
     *      path="/api/daily-report",
     *      operationId="getDailyReportList",
     *      tags={"A03-S01"},
     *      summary="Get list daily report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="startDate",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="endDate",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="mode_week",in="query", @OA\Schema(type="bool")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "200",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function getListData(ReportRequest $request)
    {
        $startDate = $request->report_period_start_date;
        $endDate = $request->report_period_end_date;
        $data = $this->service->getList($request->org_cd, $request->mode_week, $startDate, $endDate);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/daily-report/mode",
     *      operationId="getModeDailyReport",
     *      tags={"A03-S01"},
     *      summary="Get mode daily report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "200",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function getModeReport()
    {
        $user_login = $this->getCurrentUser();
        $data = $this->service->getModeReport($user_login);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/daily-report/detail",
     *      operationId="getDailyReportDetail",
     *      tags={"A03-S02"},
     *      summary="Get list daily report detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="report_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_period_start_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_period_end_date",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "200",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function getDataDetail(ReportDetailRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getData($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\PUT(
     *      path="/api/daily-report/vacation",
     *      operationId="vacationReport",
     *      tags={"A03-S02"},
     *      summary="Register Vacation Report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="report_detail",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.report_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.org_short_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.report_period_end_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.report_period_start_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.report_status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.submission_remarks",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.comment",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_schedule",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_schedule.*.report_detail_note",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_schedule.*.schedule_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_schedule.*.schedule_type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "200",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function registerVacationReport(SaveReportRequest $request)
    {
        DB::beginTransaction();
        try {
            if ($this->modeReport('報告')) {
                return $this->responseErrorForbidden('週報の場合は休暇を対応できません。');
            }
            if (!$this->checkEventReportByDateSysTem($request->report_period_start_date)) {
                return $this->responseErrorForbidden("報告は" . $request->report_period_start_date . "の未来日で登録できません。");
            }
            $request->user_login = $this->getCurrentUser();
            $data = $this->service->registerVacationReport($request);
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    public function checkEventReportByDateSysTem($report_period_start_date)
    {
        $dateSystem = date_create($this->date)->format('Y-m-d');
        return strtotime($dateSystem) >= strtotime($report_period_start_date) ? true : false;
    }

    /**
     * @OA\DELETE(
     *      path="/api/daily-report/vacation",
     *      operationId="deleteVacationReport",
     *      tags={"A03-S02"},
     *      summary="Delete Vacation Report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="report_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "200",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function deleteVacationReport(VacationReportRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->service->deleteVacationReport($request);
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
     *      path="/api/daily-report/temporarily-save",
     *      operationId="TemporarilySaveReport",
     *      tags={"A03-S02"},
     *      summary="Insert or Update Report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="report_detail",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.report_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.org_short_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.report_period_end_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.report_period_start_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.report_status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.submission_remarks",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_detail.*.comment",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_schedule",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_schedule.*.report_detail_note",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_schedule.*.schedule_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_schedule.*.schedule_type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "201",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function saveReport(SaveReportRequest $request)
    {
        DB::beginTransaction();
        try {
            $report_detail = (object)$request->report_detail[0];
            if (!$this->checkEventReportByDateSysTem($report_detail->report_period_start_date)) {
                return $this->responseErrorForbidden("報告は" . $report_detail->report_period_start_date . "の未来日で登録できません。");
            }
            $request->user_cd = $this->getCurrentUser();
            $data = $this->service->saveReport($request);
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
     *      path="/api/daily-report/submit",
     *      operationId="SubmitReport",
     *      tags={"A03-S02"},
     *      summary="Submit Report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="report_detail",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="list_schedule",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "201",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function submitReport(SaveReportRequest $request)
    {
        DB::beginTransaction();
        try {
            $report_detail = (object)$request->report_detail[0];
            if (!$this->checkEventReportByDateSysTem($report_detail->report_period_start_date)) {
                return $this->responseErrorForbidden("報告は" . $report_detail->report_period_start_date . "の未来日で登録できません。");
            }
            $request->user_login = $this->getCurrentUser();
            $user_approval_knowledge = $this->service->getUserApproval($request->user_login);
            if(empty($user_approval_knowledge)){
                return $this->responseErrorValidate(__('daily_report.no_approval'));
            }
            $data = $this->service->submitReport($request);
            DB::commit();
            return $this->responseCreated('success', $data);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\DELETE(
     *      path="/api/daily-report/submit",
     *      operationId="DeleteSubmitReport",
     *      tags={"A03-S02"},
     *      summary="Delete Report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="report_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "201",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function cancelReport(DailyReportRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->user_login = $this->getCurrentUser();
            $request->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_report)->definition_value ?? null;
            $report_detail = $this->repo->getReportDetail($request->report_id, $request->request_type,$request->user_login);
            if ($report_detail->report_status_type != 20) {
                return $this->responseErrorForbidden('この報告を承認されたため、提出取り消しができません。');
            }
            $this->service->cancelReport($request);
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
     *      path="/api/daily-report/approval",
     *      operationId="ApprovalReport",
     *      tags={"A03-S02"},
     *      summary="Approval Report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="comment",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "201",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function approvalReport(ApprovalReportRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->user_login = $this->getCurrentUser();
            $request->user_name = $this->authService->getInfoUser($request->user_login)->user_name;
            $this->service->approvalReport($request);
            DB::commit();
            return $this->responseCreated('success');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\DELETE(
     *      path="/api/daily-report/approval",
     *      operationId="CancelApprovalReport",
     *      tags={"A03-S02"},
     *      summary="Cancel Approval Report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="comment",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="report_status_type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "201",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function cancelApprovalReport(ApprovalReportRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->user_login = $this->getCurrentUser();
            $request->user_name = $this->authService->getInfoUser($request->user_login)->user_name;
            $this->service->cancelApprovalReport($request);
            DB::commit();
            return $this->responseCreated('success');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }
}
