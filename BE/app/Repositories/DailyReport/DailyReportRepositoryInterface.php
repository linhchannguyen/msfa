<?php

namespace App\Repositories\DailyReport;

use App\Repositories\BaseRepositoryInterface;

interface DailyReportRepositoryInterface extends BaseRepositoryInterface
{
    public function getList($organization, $startDate, $endDate);
    public function getListDataOfWeek($organization, $startDate, $endDate);
    public function listStatusType();
    public function getReportDetail($report_id, $request_type, $user_login);
    public function getVacationReport($report_id);
    public function getDataDetail($date, $report_id, $user_login);
    public function getScheduleTypeCall($schedule_id);
    public function getDetailByCallID($call_id);
    public function getScheduleTypeConvention($schedule_id);
    public function getScheduleTypeBriefing($schedule_id);
    public function getScheduleTypeOfficeSchedule($schedule_id);
    public function registerVacationReport($data);
    public function deleteVacationReport($data);
    public function saveReport($data);
    public function updateReportDetail($data);
    public function saveDetailReport($report_id, $schedule_id, $schedule_type, $report_detail_note);
    public function submitReport($data);
    public function cancelApproval($data);
    public function approvalReport($report_id, $status_type, $request_type);
    public function approvalDetailReport($data);
    public function cancelReport($data);
    public function getUserApproval($user_login, $approval_work_type, $report_id, $user_report, $request_type, $layer_num);
    public function getUserApprovalReport($user_login, $approval_work_type, $report_id, $user_report, $request_type, $layer_num);
    public function approvalRequestDetail($report_id, $comment, $user_login, $status_type);
    public function approvalRequest($report_id, $request_type, $status_type, $user_login);
    public function getStatusTypeReport($report_id);
    public function modeReport($parameter);
    public function checkUserApproval($approval_work_type, $user_login);
    public function getLayerNumApprovalFinnal();
    public function checkUserApprovalFinal($approval_work_type, $user_login, $user_create_convention);
    public function getRequestIDApproval($data);
    public function deleteApprovalRequest($request_id);
    public function updateActiveLayerNumRequest($active_layer_num, $request_id);
}
