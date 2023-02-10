<?php

namespace App\Traits;

use App\Services\AuthService;
use App\Services\DevelopService;
use App\Repositories\DailyReport\DailyReportRepository;
use App\Enums\ReportType;
use App\Enums\StatusApprovalType;

trait StatusReportTrait
{
    public function getStatusTypeReport($report_id)
    {
        $repo = new DailyReportRepository();
        $status = $repo->getStatusTypeReport($report_id);
        $status_report = "";
        if (!$report_id || !isset($status->report_id)) {
            $status_report = __(ReportType::describe(ReportType::TYPE_NON_REGISTER));
        } elseif ($status->report_status_type == ReportType::STATUS_CREATED && !$status->status_type) {
            $status_report = __(ReportType::describe(ReportType::TYPE_REGISTER));
        } elseif ($status->report_status_type == ReportType::STATUS_SUBMIT && $status->status_type == StatusApprovalType::STATUS_REGISTER) {
            $status_report = __(ReportType::describe(ReportType::TYPE_SUBMIT));
        } elseif ($status->report_status_type == ReportType::STATUS_APPROVAL && $status->status_type == StatusApprovalType::STATUS_SUBMIT) {
            $status_report = __(ReportType::describe(ReportType::TYPE_APPROVAL));
        } elseif ($status->report_status_type == ReportType::STATUS_CREATED && $status->status_type == StatusApprovalType::STATUS_REMAND) {
            $status_report = __(ReportType::describe(ReportType::TYPE_REMAND));
        }
        return $status_report;
    }

    public function modeReport($parameter)
    {
        $repo = new DailyReportRepository();
        $status = $repo->modeReport($parameter);
        return $status->parameter_value == 1 ? 0 : 1;
    }

    public function getNumberStatusReport($status_report)
    {
        $status_number = 0;
        switch($status_report){
            case '差戻':
                $status_number = 3;
            break;
            case '提出済':
                $status_number = 2;
            break;
            case '未作成':
                $status_number = 1;
            break;
            case '未提出':
                $status_number = 1;
            break;
            case '承認済':
                $status_number = 4;
            break;
            default:
            $status_number = 0;
        }
        return $status_number;
    }
}
