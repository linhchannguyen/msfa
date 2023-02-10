<?php

namespace App\Services;

use App\Repositories\InterviewDetails\InterviewDetailsRepositoryInterface;
use App\Traits\ArrayTrait;
use App\Traits\StatusReportTrait;
use App\Repositories\Inform\InformRepositoryInterface;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Repositories\DailyReport\DailyReportRepository;

class InterviewDetailsService
{
    use ArrayTrait, StatusReportTrait;
    private $repo;

    public function __construct(InterviewDetailsRepositoryInterface $repo, InformRepositoryInterface $inform, ScheduleRepositoryInterface $schedule)
    {
        $this->repo = $repo;
        $this->inform = $inform;
        $this->schedule = $schedule;
    }

    public function getScreenData()
    {
        $data['title_office'] = $this->repo->getTitleOffice();
        return $data;
    }

    public function getActiveDate($schedule_id)
    {
        $activeDate = $this->repo->getActiveDate($schedule_id);
        if (!empty($activeDate->schedule_id ?? '')) {
            $activeDate->report_status_type = $this->getStatusTypeReport($activeDate->report_id ?? '');
        }
        return $activeDate;
    }

    public function getInterviewContent($schedule_id)
    {
        $department = $this->repo->getDepartmentInfo($schedule_id);
        if (count($department) > 0) {
            foreach ($department as $item) {
                $item = (object)$item;
                $datas = $this->repo->getContent($item->call_id) ?? [];
                if (count($datas) > 0) {
                    foreach ($datas as $data) {
                        $data = (object)$data;
                        $data->data = [];
                        if (!empty($data->detail_id ?? '')) {
                            $data->data = $this->repo->getDetailDocument($data->detail_id);
                        }
                    }
                }
                $item->dataTable = $datas;
            }
        }
        return $department;
    }

    public function getDateTimeSetting($schedule_id)
    {
        $datas = $this->repo->getDateTimeSetting($schedule_id);
        if (!empty($datas->visit_id ?? '')) {
            $datas->accompanying_user = $this->repo->getAccompanyingUserDateTime($datas->visit_id);
            $datas->report_status_type = null;
            $activeDate = $this->repo->getActiveDate($schedule_id);
            if (!empty($activeDate->schedule_id ?? '')) {
                $datas->report_status_type = $this->getStatusTypeReport($activeDate->report_id ?? '');
            }
            $datas->report_id = $activeDate->report_id;
        }
        return $datas;
    }

    public function deleteInterviewer($schedule_id, $facility_short_name, $visit_id, $call_id)
    {
        $this->repo->deleteAllInterviewContent($visit_id, $call_id);
        $title = $this->getTitle($schedule_id, $facility_short_name);
        $display_option_type = $this->checkPlan($schedule_id, $visit_id);
        return $this->repo->updateScheduleTitle($schedule_id, $title, $display_option_type);
    }

    public function saveSchedule($schedule_id, $facility_short_name, $visit_id, $start_date, $start_time, $end_time, $all_day_flag)
    {
        $title = $this->getTitle($schedule_id, $facility_short_name);
        $display_option_type = $this->checkPlan($schedule_id, $visit_id);
        return $this->repo->saveSchedule($schedule_id, $facility_short_name, $visit_id, $start_date, $start_time, $end_time, $all_day_flag, $title, $display_option_type);
    }

    public function getTitle($schedule_id, $facility_short_name)
    {
        $title = $person_name = '';
        $number_person_cd = $this->repo->getNumberPerson($schedule_id);

        if ($number_person_cd == 0) {
            $title = $facility_short_name;
        } elseif ($number_person_cd == 1) {
            $person_name = $this->repo->getTitlePerson($schedule_id);
            $title = $facility_short_name . ' ' . $person_name;
        } elseif ($number_person_cd > 1) {
            $title = $facility_short_name . ' ' . $number_person_cd . "名";
        }
        return $title;
    }

    public function checkPlan($schedule_id, $visit_id)
    {
        $t_visit = $this->repo->getVisit($schedule_id, $visit_id);
        $important_flag = $t_visit->important_flag ?? 0;

        $t_call = $this->repo->getTCall($visit_id);
        if(empty($t_call)){
            return 'V99';
        }

        $plan_flag_temp = 0;
        $act_flag_temp = 0;

        if (count((array)$t_call) > 0) {
            foreach ($t_call as $call) {
                $call = (object)$call;
                if ($call->plan_flag == 1) {
                    $plan_flag_temp = 1;
                }
                if ($call->act_flag == 1) {
                    $act_flag_temp = 1;
                }
            }
        }

        $display_option_type = $plan_flag_temp . $act_flag_temp . $important_flag;

        switch ($display_option_type) {
            case '000':
                $display_option_type = 'C00';
                break;
            case '100':
                $display_option_type = 'C01';
                break;
            case '010':
                $display_option_type = 'C02';
                break;
            case '110':
                $display_option_type = 'C03';
                break;
            case '001':
                $display_option_type = 'C10';
                break;
            case '101':
                $display_option_type = 'C11';
                break;
            case '011':
                $display_option_type = 'C12';
                break;
            case '111':
                $display_option_type = 'C13';
                break;
            default:
                $display_option_type = 'C01';
        }

        return $display_option_type;
    }



    public function saveVisit($visit_id, $schedule_id, $user_name, $org_cd, $org_short_name, $important_flag, $remarks, $user_cd, $start_date, $start_date_old)
    {
         // check report start date old
        $data = $this->repo->checkReportStartDate($start_date_old, $user_cd);
        if (!empty($data->report_id ?? '')) {
            $status_report = $this->getStatusTypeReport($data->report_id ?? '');
            if (trim($status_report) == "提出済" || trim($status_report) == "承認済") {
                return false;
            }
        }
         // check report start date new
        if (date_format(date_create($start_date_old), 'Y-m-d') !=  date_format(date_create($start_date), 'Y-m-d')) {
            $data = $this->repo->checkReportStartDate($start_date, $user_cd);
            if (!empty($data->report_id ?? '')) {
                $status_report = $this->getStatusTypeReport($data->report_id ?? '');
                if (trim($status_report) == "提出済" || trim($status_report) == "承認済") {
                    return false;
                }
            }
        }

        $this->repo->saveVisit($visit_id, $schedule_id, $user_name, $org_cd, $org_short_name, $important_flag, $remarks);
        return true;
    }

    public function saveAccompanyingUser($visit_id, $user_cd, $user_name, $org_cd, $org_short_name)
    {
        return $this->repo->saveAccompanyingUser($visit_id, $user_cd, $user_name, $org_cd, $org_short_name);
    }

    public function checkAccompanyingUser($visit_id, $user_cd)
    {
        return $this->repo->checkAccompanyingUser($visit_id, $user_cd) > 1 ? true : false;
    }

    public function deleteAccompanyingUser($visit_id)
    {
        return $this->repo->deleteAccompanyingUser($visit_id);
    }

    public function createInform($schedule_id, $inform_contents, $user_cd, $inform_category_cd)
    {
        $not_received_inform_list = $this->inform->installed($user_cd);
        //Check if the user is set to receive notification type
        foreach ($not_received_inform_list as $value) {
            if ($value->inform_category_cd == $inform_category_cd && $value->checked == 0) {
                return true;
            }
        }
        $inform_id = $this->inform->registerInform($user_cd, $user_cd, $inform_contents, $inform_category_cd);
        return $this->inform->registerInformParameter($user_cd, $inform_id, 'calendar', $schedule_id);
    }

    public function getAccompanyingUser($visit_id)
    {
        return $this->repo->getAccompanyingUser($visit_id);
    }

    public function deleteSchedule($schedule_id, $visit_id)
    {
        $result = $this->checkStatusReport($schedule_id);
        if (!$result) {
            return false;
        }
        $this->repo->deleteSchedule($schedule_id);
        return true;
    }

    public function deleteAllInterview($schedule_id, $visit_id)
    {
        $this->repo->deleteAllInterviewContent($visit_id, null);
        $t_visit = $this->repo->getVisit($schedule_id, $visit_id);
        $facility_short_name = $t_visit->facility_short_name ?? '';
        $title = $this->getTitle($schedule_id, $facility_short_name);
        $display_option_type = $this->checkPlan($schedule_id, $visit_id);
        return $this->repo->updateScheduleTitle($schedule_id, $title, $display_option_type);
    }

    public function getInternalSchedule($schedule_id)
    {
        return $this->repo->getInternalSchedule($schedule_id);
    }

    public function deleteInternalSchedule($schedule_id, $office_schedule_id)
    {
        $result = $this->checkStatusReport($schedule_id);
        if (!$result) {
            return false;
        }
        $this->repo->deleteInternalSchedule($schedule_id, $office_schedule_id);
        return true;
    }

    public function checkStatusReport($schedule_id)
    {
        $data = $this->repo->getStatusReport($schedule_id);
        $status_report = $this->getStatusTypeReport($data->report_id ?? '');
        if (trim($status_report) == "提出済" || trim($status_report) == "承認済") {
            return false;
        }
        return true;
    }

    public function saveInternalSchedule($schedule_id, $title, $office_schedule_type, $start_date, $start_time, $end_time, $all_day_flag, $remarks, $user_cd_login, $start_date_old)
    {
        // check report start date old
        $data = $this->repo->checkReportStartDate($start_date_old, $user_cd_login);
        if (!empty($data->report_id ?? '')) {
            $status_report = $this->getStatusTypeReport($data->report_id ?? '');
            if (trim($status_report) == "提出済" || trim($status_report) == "承認済") {
                return false;
            }
        }

        // check report start date new
        
        if ( date_format(date_create($start_date_old), 'Y-m-d') !=  date_format(date_create($start_date), 'Y-m-d')) {
            $data = $this->repo->checkReportStartDate($start_date, $user_cd_login);
            if (!empty($data->report_id ?? '')) {
                $status_report = $this->getStatusTypeReport($data->report_id ?? '');
                if (trim($status_report) == "提出済" || trim($status_report) == "承認済") {
                    return false;
                }
            }
        }

        $this->repo->updateInternalSchedule($schedule_id, $title, $office_schedule_type, $start_date, $start_time, $end_time, $all_day_flag);
        $this->repo->updateOfficeSchedule($schedule_id, $title, $office_schedule_type, $remarks);
        return true;
    }

    public function getPrivateSchedule($schedule_id)
    {
        return $this->repo->getPrivateSchedule($schedule_id);
    }

    public function savePrivateSchedule($schedule_id, $title, $start_date, $start_time, $end_time, $all_day_flag, $remarks)
    {
        $update_private_schedule = $this->repo->updatePrivateSchedule($schedule_id, $title, $start_date, $start_time, $end_time, $all_day_flag);
        $update_private = $this->repo->updatePrivate($schedule_id, $title, $remarks);
        return $update_private_schedule && $update_private;
    }

    public function deletePrivateSchedule($schedule_id)
    {
        return $this->repo->deletePrivateSchedule($schedule_id);
    }

    public function checkPersonCd($visit_id, $dataStock)
    {
        $sum = 0;
        if (count($dataStock) > 0) {
            foreach ($dataStock as $stock) {
                $stock = (object)$stock;
                $data = $this->repo->getCallPerson($visit_id, $stock->person_cd);
                if (($data->act_flag ?? 0) == 1) {
                    $sum += 1;
                }
            }
        }
        return $sum;
    }

    public function addStock($visit_id, $schedule_id, $stock, $data_user, $date_system)
    {
        $user_cd = $data_user->user_cd;
        $user_name = $data_user->user_name;
        $org_cd = $data_user->org_cd;
        $org_short_name = $data_user->org_short_name;

        if (count($stock) > 0) {
            foreach ($stock as $Item) {
                $Item = (object)$Item;
                $data = $this->repo->getCallPerson($visit_id, $Item->person_cd);
                $call_id = $data->call_id ?? 0;
                if (empty($data->visit_id ?? '')) {
                    $plan_flag = 0;
                    $act_flag = 0;
                    $t_schedule = $this->repo->getSchedule($schedule_id);
                    $start_date = $t_schedule->start_date ?? '';
                    // set plan_flag and act_flag
                    if (date_format(date_create($start_date), 'Y-m-d') > date_format(date_create($date_system), 'Y-m-d')) {
                        $plan_flag = 1;
                        $act_flag = 0;
                    } elseif (date_format(date_create($start_date), 'Y-m-d') <= date_format(date_create($date_system), 'Y-m-d')) {
                        $plan_flag = 0;
                        $act_flag = 0;
                    }
                    $m_position = $this->repo->getPosition($Item->facility_cd, $Item->person_cd);
                    $display_position_name = $m_position->position_name ?? '';
                    $t_call = $this->repo->insertCall($visit_id, $Item->person_cd, $Item->person_name, $Item->department_cd, $Item->department_name, $plan_flag, $act_flag, $display_position_name);
                    $call_id = $t_call->call_id ?? '';
                    //update t_schedule
                    $facility_short_name = $Item->facility_short_name;
                    $title = $this->getTitle($schedule_id, $facility_short_name);
                    $display_option_type = $this->checkPlan($schedule_id, $visit_id);
                    $this->repo->updateScheduleTitle($schedule_id, $title, $display_option_type);
                }

                if (count((array)$Item->product_list) > 0 && !empty($Item->content_cd)) {
                    foreach ($Item->product_list as $product_list) {
                        $product_list = (object)$product_list;
                        // insert t_detail
                        $t_detail_temp = $this->repo->getDetailOrder($call_id);
                        $detail_order = empty($t_detail_temp->detail_order ?? 0) ?  1 : $t_detail_temp->detail_order + 1;
                        $product_cd = $product_list->product_cd;
                        $product_name = $product_list->product_name;
                        if (!empty($Item->content_cd) && !empty($product_cd)) {
                            $t_detail = $this->repo->insertDetail($call_id, $detail_order, $Item->content_cd, $Item->content_name, $product_cd, $product_name);
                            $detail_id = $t_detail->detail_id ?? '';
                            foreach ($Item->document_list as $item) {
                                $item = (object)$item;
                                $document_id = $item->document_id;
                                $this->repo->insertDetailDocument($detail_id, $document_id);
                            }
                        }
                        break;
                    }
                }
                // new
                $this->repo->updateVisitAddStock($user_cd, $user_name, $org_cd, $org_short_name, $visit_id);
                // update status t_stock
                $this->repo->updateStatusStock($Item->stock_id);
            }
        }

        return true;
    }

    public function addInterviewDestination($facility_cd, $visit_id, $person, $schedule_id, $facility_short_name, $date_system)
    {
        foreach($person as $value){
            $act_flag = 0;
            $t_call = $this->repo->getCallPerson($visit_id, $value['person_cd']);
            $t_schedule = $this->repo->getSchedule($schedule_id);
            $start_date = $t_schedule->start_date ?? '';
            $m_position = $this->repo->getPosition($facility_cd, $value['person_cd']);
            $display_position_name = $m_position->position_name ?? '';
    
            $plan_flag = 0;
    
            if (date_create($start_date)->format('Y-m-d') > date_create($date_system)->format('Y-m-d')) {
                $plan_flag = 1;
            }
    
            if (!empty($t_call->call_id ?? '')) {
                $this->repo->updateCall($visit_id, $value['person_cd'], $value['person_name'], $value['department_cd'], $value['department_name'], $plan_flag);
            } else {
                $this->repo->insertCall($visit_id, $value['person_cd'], $value['person_name'], $value['department_cd'], $value['department_name'], $plan_flag, $act_flag, $display_position_name);
            }
        }

        $title = $this->getTitle($schedule_id, $facility_short_name);
        $display_option_type = $this->checkPlan($schedule_id, $visit_id);

        return $this->repo->updateScheduleTitle($schedule_id, $title, $display_option_type);
    }

    public function getSchedule($schedule_id)
    {
        return $this->repo->getSchedule($schedule_id);
    }

    public function createPlanSchedule($visit_id, $schedule_id, $data_user)
    {
        $t_schedule_temp = $this->repo->getSchedule($schedule_id);
        // save accompanying user
        $getAccompanyingUser = $this->getAccompanyingUser($visit_id);
        array_push($getAccompanyingUser, $data_user->schedule_user);
        if (!empty($t_schedule_temp->schedule_id ?? '')) {
            $t_schedule = $this->repo->createSchedule($t_schedule_temp, $data_user);
            $t_visit = $this->repo->getVisitCopyPlan($visit_id);
            $t_visit_temp = $this->repo->createVisitCopyPlan($t_schedule->schedule_id, $t_visit, $data_user);
            foreach($getAccompanyingUser as $item){
                if($item->user_cd != $data_user->user_cd){
                    $this->repo->saveAccompanyingUser($t_visit_temp->visit_id, $item->user_cd, $item->user_name ?? null, $item->org_cd, $item->org_short_name ?? null);
                }
            }
            $t_call = $this->repo->getTCall($visit_id);
            if (count($t_call) > 0) {
                foreach ($t_call as $item) {
                    $this->repo->insertCall($t_visit_temp->visit_id, $item->person_cd, $item->person_name, $item->department_cd, $item->department_name, $item->plan_flag, $item->act_flag, $item->display_position_name);
                }
            }
        }
        return $t_schedule ?? [];
    }
}
