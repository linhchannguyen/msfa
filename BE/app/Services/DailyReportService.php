<?php

namespace App\Services;

use App\Traits\StatusReportTrait;

use App\Repositories\Auth\AuthRepository;
use App\Repositories\Inform\InformRepositoryInterface;
use App\Repositories\DailyReport\DailyReportRepositoryInterface;
use App\Repositories\Organization\OrganizationRepositoryInterface;
use App\Repositories\VariableDefinition\VariableDefinitionRepositoryInterface;
use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;

class DailyReportService
{
    use StatusReportTrait;
    private $repo, $auth, $org, $inform, $serviceDate, $date, $variable, $convention, $category_name_submit, $category_name_approval, $category_name_remand;

    public function __construct(
        DailyReportRepositoryInterface $repo,
        OrganizationRepositoryInterface $org,
        InformRepositoryInterface $inform,
        SystemParameterService $serviceDate,
        VariableDefinitionRepositoryInterface $variable,
        ConventionSearchRepositoryInterface $convention
    ) {
        $this->repo = $repo;
        $this->convention = $convention;
        $this->org = $org;
        $this->inform = $inform;
        $this->variable = $variable;
        $this->auth = new AuthRepository();
        $this->serviceDate = $serviceDate;
        $this->date = $this->serviceDate->getCurrentSystemDateTime();
        $this->definition_name_report = "承認業務区分";
        $this->definition_label_report = "報告";
        $this->category_name_submit = '日報提出';
        $this->category_name_approval = '日報承認';
        $this->category_name_remand = '日報差戻し';
    }

    public function getModeReport($user_login)
    {
        $result['mode_week'] =  $this->repo->modeReport($this->definition_label_report)->parameter_value == 1 ? 0 : 1;
        $aprroval_type_report = $this->variable->getVariableByDefinitionNameAndLabel($this->definition_name_report, $this->definition_label_report);
        $result['user_approval'] = $this->repo->checkUserApproval($aprroval_type_report->definition_value, $user_login);
        return $result;
    }

    public function getList($organization, $mode_week, $startDate, $endDate)
    {
        if (!$mode_week) {
            $result = $this->getListOfDay($organization, $startDate, $endDate);
        } else {
            $result = $this->getListOfWeek($organization, $startDate);
        }
        return $result;
    }

    public function getListOfDay($organization, $startDate, $endDate)
    {
        $startDate = date('Y-m-d', strtotime('-7 days', strtotime($startDate)));
        $report = $this->repo->getList($organization, $startDate, $endDate);
        $data = [];
        if (count($report) > 0) {
            array_walk($report, function (&$item) use (&$data) {
                if ($item->report_period_start_date === $item->report_period_end_date) {
                    $item->status = $this->getStatusTypeReport($item->report_id);
                    $item->vacation_flg = $item->vacation_flg ? 1 : 0;
                    array_push($data, $item);
                }
            });
        }
        return $data;
    }

    public function getListOfWeek($organization, $startDate)
    {
        $array_date = [];
        $date = date('Y-m-d', strtotime('-1 month', strtotime($startDate)));
        $month_old = date("m", strtotime(date('Y-m-d', strtotime('+1 month', strtotime($startDate)))));
        for ($i = 0; $i <= 10; $i++) {
            if ($i == 0) {
                $monday = date('Y-m-d', strtotime('first monday of this month', strtotime($date)));
            } else {
                $monday = date('Y-m-d', strtotime('monday this week', strtotime($date)));
            }
            $sunday = date('Y-m-d', strtotime('sunday this week', strtotime($monday)));
            $month_new = date("m", strtotime($date));
            if ($month_old === $month_new) {
                break;
            }
            $obj = array(
                'startDate' => $monday,
                'endDate' => $sunday
            );
            array_push($array_date, $obj);
            $date = date('Y-m-d', strtotime('+7 days', strtotime($monday)));
        }
        foreach ($array_date as &$item) {
            $report = $this->repo->getListDataOfWeek($organization, $item['startDate'], $item['endDate']);
            if (count($report) > 0) {
                foreach ($report as &$item_report) {
                    $item_report->status = $this->getStatusTypeReport($item_report->report_id);
                    $item_report->vacation_flg = $item_report->vacation_flg ? 1 : 0;
                }
            }
            $item['list_report'] = $report;
        }
        return $array_date;
    }

    public function getData($request)
    {
        $request->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_report)->definition_value ?? null;
        $aprroval_type_report = $this->variable->getVariableByDefinitionNameAndLabel($this->definition_name_report, $this->definition_label_report);
        $report_detail = $this->repo->getReportDetail($request->report_id, $request->request_type, $request->user_login);
        $vacation = $this->repo->getVacationReport($request->report_id);
        $user_approval = $this->repo->getUserApprovalReport(null, $aprroval_type_report->definition_value, $request->report_id, $report_detail->user_cd ?? null, $request->request_type, null);
        $user_check = $this->repo->getUserApprovalReport($request->user_login, $aprroval_type_report->definition_value, $request->report_id, $report_detail->user_cd ?? null, $request->request_type, null);
        $result['status_report'] =  $this->getStatusTypeReport($request->report_id);
        $result['user_check'] = count($user_check) > 0 ? true : false;
        $result['layer_user_approval'] = $user_check;
        $count_date = 0;

        if (isset($report_detail->report_id)) {
            $datediff = abs(strtotime($report_detail->report_period_start_date) - strtotime($report_detail->report_period_end_date));
            $count_date = floor($datediff / (60 * 60 * 24));
            $newdate = $report_detail->report_period_start_date;
            $report_detail->request_type = $request->request_type;
            $approval_request = $this->repo->getRequestIDApproval($report_detail);
            $result['active_layer_approval'] = isset($approval_request->request_id) ? $approval_request->active_layer_num : 1;
        } else {
            if ($request->report_period_start_date === $request->report_period_end_date) {
                $count_date = 0;
                $newdate = $request->report_period_start_date;
            } else {
                $datediff = abs(strtotime($request->report_period_start_date) - strtotime($request->report_period_end_date));
                $count_date = floor($datediff / (60 * 60 * 24));
                $newdate = $request->report_period_start_date;
            }
        }
        $list_schedule = [];
        if (isset($vacation->report_id)) {
            $result['vacation'] = true;
        } else {
            $result['vacation'] = false;
            for ($i = 0; $i <= (int)$count_date; $i++) {
                $schedule = [];
                $user_create_report = isset($report_detail->report_id) ? $report_detail->user_cd : $request->user_login;
                $schedule_detail = $this->repo->getDataDetail($newdate, $request->report_id, $user_create_report);
                if (count($schedule_detail) > 0) {
                    foreach ($schedule_detail as $schedule_item) {
                        $item = [];
                        if (date('H:i:s', strtotime($schedule_item->start_time)) == '00:00:00' && date('H:i:s', strtotime($schedule_item->end_time)) == '23:59:59') {
                            $schedule_item->all_day_flag = 1;
                        } else {
                            $schedule_item->all_day_flag = 0;
                        }
                        if ($schedule_item->schedule_type == 10) {
                            $visit = $this->repo->getScheduleTypeCall($schedule_item->schedule_id);

                            foreach ($visit as $itemVisit) {
                                $schedule_item->detail = $this->repo->getDetailByCallID($itemVisit->call_id ?? null);
                                $schedule_item->facility_short_name = $itemVisit->facility_short_name ?? "";
                                $schedule_item->org_label = $itemVisit->org_short_name ?? "";
                                $schedule_item->department_name = $itemVisit->department_name ?? "";
                                $schedule_item->person_cd = $itemVisit->person_cd ?? "";
                                $schedule_item->person_name = $itemVisit->person_name ?? "";
                                $schedule_item->display_position_name = $itemVisit->display_position_name ?? "";
                                $schedule_item->off_label_flag = $itemVisit->off_label_flag ?? "";
                                $schedule_item->user_name = $itemVisit->user_name ?? "";
                                $schedule_item->call_id = $itemVisit->call_id ?? "";
                                $schedule_item->report_detail_note = $schedule_item->report_detail_note ?? "";
                                $schedule_item->schedule_id = $schedule_item->schedule_id ?? "";

                                array_push($schedule, (array)$schedule_item);
                            }
                        } elseif ($schedule_item->schedule_type == 20) {
                            $item = $this->repo->getScheduleTypeBriefing($schedule_item->schedule_id);
                        } elseif ($schedule_item->schedule_type == 30) {
                            $item = $this->repo->getScheduleTypeConvention($schedule_item->schedule_id);
                        } elseif ($schedule_item->schedule_type == 40) {
                            $item = $this->repo->getScheduleTypeOfficeSchedule($schedule_item->schedule_id);
                        }
                        foreach ($item as $itemSchedule) {
                            if (isset($itemSchedule->schedule_type)) {
                                if (date('H:i:s', strtotime($itemSchedule->start_time)) == '00:00:00' && date('H:i:s', strtotime($itemSchedule->end_time)) == '23:59:59') {
                                    $itemSchedule->all_day_flag = 1;
                                } else {
                                    $itemSchedule->all_day_flag = 0;
                                }
                                $itemSchedule->report_detail_note = $schedule_item->report_detail_note ?? "";
                                $itemSchedule->schedule_id = $schedule_item->schedule_id;
                                array_push($schedule, (array)$itemSchedule);
                            }
                        }
                    }
                }
                array_multisort(
                    array_column($schedule, 'all_day_flag'),
                    SORT_DESC,
                    array_column($schedule, 'start_time'),
                    SORT_DESC,
                    $schedule
                );
                $obj_schedule['date'] = $newdate;
                $obj_schedule['schedule'] = $schedule;
                $newdate = date('Y/m/d', strtotime('+1 day', strtotime($newdate)));
                if (count($schedule) > 0) {
                    array_push($list_schedule, $obj_schedule);
                }
            }
        }
        $result['list_status_type'] =  $this->repo->listStatusType();
        $result['user_approval'] = $user_approval;
        $result['report_detail'] = $report_detail;
        $result['list_schedule'] =  $list_schedule;
        return $result;
    }

    public function registerVacationReport($data)
    {
        $report = $this->saveReport($data);
        $data->report_id = $report->report_id;
        $this->repo->registerVacationReport($data);
        return $report;
    }

    public function deleteVacationReport($data)
    {
        return $this->repo->deleteVacationReport($data);
    }

    public function saveReport($data)
    {
        $report_detail = (object)$data->report_detail[0];
        if (!$report_detail->report_id) {
            $report_detail = $this->repo->saveReport($report_detail);
        } else {
            $this->repo->updateReportDetail($report_detail);
        }
        if (count($data->list_schedule) > 0) {
            foreach ($data->list_schedule as $item) {
                $obj_schedule = (object)$item;
                if (count($obj_schedule->schedule) > 0) {
                    foreach ($obj_schedule->schedule as $element) {
                        $this->repo->saveDetailReport($report_detail->report_id, $element['schedule_id'], $element['schedule_type'], $element['report_detail_note']);
                    }
                }
            }
        }
        return $report_detail;
    }

    public function getUserApproval($user_cd){
        $aprroval_type_report = $this->variable->getVariableByDefinitionNameAndLabel($this->definition_name_report, $this->definition_label_report);
        return $this->convention->getUserApproval($aprroval_type_report->definition_value, null, $user_cd);
    }

    public function submitReport($data)
    {
        $report_detail = $this->saveReport($data);
        $report_detail->comment = $data->report_detail[0]['comment'];
        $report_detail->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_report)->definition_value ?? null;
        $report_detail->status_type = 0;
        $report_detail->layer_num = 1;
        $aprroval_type_report = $this->variable->getVariableByDefinitionNameAndLabel($this->definition_name_report, $this->definition_label_report);
        $user_approval = $this->convention->getUserApproval($aprroval_type_report->definition_value, null, $report_detail->user_cd);
        $report_detail->approval_user_cd = $data->user_login;
        $approval_request = $this->repo->getRequestIDApproval($report_detail);
        if (isset($approval_request->request_id)) {
            $this->repo->deleteApprovalRequest($approval_request->request_id);
        }
        $approval = $this->repo->submitReport($report_detail);
        $inform_category = $this->convention->getInformCategory($this->category_name_submit);
        foreach ($user_approval as $item) {
            $report_detail->approval_user_cd = $item->approval_user_cd;
            $report_detail->request_id = $approval->request_id;
            $report_detail->comment = null;
            $report_detail->layer_num = $item->approval_layer_num;
            $this->repo->approvalDetailReport($report_detail);
        }
        $user_approval_layer_1 = $this->repo->getUserApprovalReport(null, $aprroval_type_report->definition_value, $report_detail->report_id, $report_detail->user_cd, $report_detail->request_type, 1);
        foreach ($user_approval_layer_1 as $item_layer_1) {
            $report_detail->approval_user_cd = $item_layer_1->approval_user_cd;
            $check_recieve_mail = true;
            $this->checkReceiveInform($inform_category->inform_category_cd, $item_layer_1->approval_user_cd, $check_recieve_mail);
            if($check_recieve_mail){
                $content = date('Y年m月d日', strtotime($report_detail->report_period_start_date)) . " " . $report_detail->user_name . "の報告の承認をお願いします。";
                $inform = $this->inform->registerInform($report_detail->user_cd, $item_layer_1->approval_user_cd, $content, $inform_category->inform_category_cd);
                $this->inform->registerInformParameter($report_detail->user_cd, $inform, "report_id", $approval->request_target_id);
            }
            $this->repo->approvalReport($report_detail->report_id, $report_detail->status_type, $report_detail->request_type);
        }

        return $report_detail;
    }

    public function cancelReport($data)
    {
        $data->report_status_type  = 10;
        $data->status_type  = 0;
        $data->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_report)->definition_value ?? null;
        $this->repo->cancelApproval($data);
        return $this->repo->cancelReport($data);
    }

    public function approvalReport($data)
    {
        $data->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_report)->definition_value ?? null;
        $data->status_type = 1;
        $data->report_status_type = 30;
        $data->layer_num = $data->active_layer_approval;
        $report_detail = $this->repo->getReportDetail($data->report_id, $data->request_type, $data->user_login);
        $content = "";
        $report_detail->request_type = $data->request_type;
        $layer_num_approval_finnal = $this->repo->getLayerNumApprovalFinnal();
        $aprroval_type_report = $this->variable->getVariableByDefinitionNameAndLabel($this->definition_name_report, $this->definition_label_report);
        $data->user_cd = $data->user_login;
        $data->approval_user_cd = $data->user_login;
        $approval = $this->repo->getRequestIDApproval($report_detail);
        $data->request_id = $approval->request_id;
        $user_approval_finnal = $this->repo->checkUserApprovalFinal($aprroval_type_report->definition_value, $data->user_login, $report_detail->user_cd);
        $inform_category = $this->convention->getInformCategory($this->category_name_approval);
        if ($data->active_layer_approval == $layer_num_approval_finnal->parameter_value && is_object($user_approval_finnal)) {
            $content = date('Y年m月d日', strtotime($report_detail->report_period_start_date)) . "の報告が承認されました。";
            $this->convention->updateConvention($data->request_id, $data->user_login, $data->status_type, $data->comment, $data->active_layer_approval);
            $check_recieve_mail = true;
            $this->checkReceiveInform($inform_category->inform_category_cd, $report_detail->user_cd, $check_recieve_mail);
            if($check_recieve_mail){
                $inform = $this->inform->registerInform($data->user_name, $report_detail->user_cd, $content, $inform_category->inform_category_cd);
                $this->inform->registerInformParameter($data->user_name, $inform, "report_id", $data->report_id);
            }
            $this->convention->updateConventionFinal($approval->request_id, $data->status_type);
            return $this->repo->cancelReport($data);
        } else {
            $this->convention->updateConvention($data->request_id, $data->user_login, $data->status_type, $data->comment, $data->active_layer_approval);
            $user_approval = $this->repo->getUserApprovalReport(null, $aprroval_type_report->definition_value, $data->report_id, $report_detail->user_cd, $data->request_type, $data->active_layer_approval + 1);
            $content = date('Y年m月d日', strtotime($report_detail->report_period_start_date)) . " " . $report_detail->user_name . "の報告の承認をお願いします。";
            foreach ($user_approval as $item) {
                $this->repo->approvalDetailReport($data);
                $check_recieve_mail = true;
                $this->checkReceiveInform($inform_category->inform_category_cd, $item->approval_user_cd, $check_recieve_mail);
                if($check_recieve_mail){
                    $inform = $this->inform->registerInform($data->user_cd, $item->approval_user_cd, $content, $inform_category->inform_category_cd);
                    $this->inform->registerInformParameter($data->user_cd, $inform, "report_id", $data->report_id);
                }
            }
            $this->repo->updateActiveLayerNumRequest($data->active_layer_approval + 1, $data->request_id);
        }
        return true;
    }

    public function cancelApprovalReport($data)
    {
        $data->status_type = 2;
        $data->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_report)->definition_value ?? null;
        $data->report_status_type = 10;
        $data->layer_num = $data->active_layer_approval;
        $report_detail = $this->repo->getReportDetail($data->report_id, $data->request_type, $data->user_login);
        $content = date('Y年m月d日', strtotime($report_detail->report_period_start_date)) . "の報告が差戻されました。";
        $inform_category = $this->convention->getInformCategory($this->category_name_remand);
        $check_recieve_mail = true;
        $this->checkReceiveInform($inform_category->inform_category_cd, $data->user_cd, $check_recieve_mail);
        if($check_recieve_mail){
            $inform = $this->inform->registerInform($data->user_name, $data->user_cd, $content, $inform_category->inform_category_cd);
            $this->inform->registerInformParameter($data->user_name, $inform, "report_id", $data->report_id);
        }
        $report_detail->request_type = $data->request_type;
        $this->variable->getVariableByDefinitionNameAndLabel($this->definition_name_report, $this->definition_label_report);
        $data->approval_user_cd = $data->user_login;
        $data->user_cd = $data->user_login;
        $approval = $this->repo->getRequestIDApproval($report_detail);
        $data->request_id = $approval->request_id;
        $this->convention->updateConventionFinal($approval->request_id, $data->status_type);
        $this->convention->updateConvention($data->request_id, $data->user_login, $data->status_type, $data->comment, $data->active_layer_approval);
        return $this->repo->cancelReport($data);
    }

    protected function checkReceiveInform($inform_category_cd, $user_login, &$check_recieve_mail)
    {
        $not_received_inform_list = $this->inform->installed($user_login);
        foreach ($not_received_inform_list as $value) {
            if ($value->inform_category_cd == $inform_category_cd && $value->checked == 0) {
                $check_recieve_mail = false;
                break;
            }
        }
    }
}
