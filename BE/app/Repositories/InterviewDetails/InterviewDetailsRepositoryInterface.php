<?php

namespace App\Repositories\InterviewDetails;

use App\Repositories\BaseRepositoryInterface;

interface InterviewDetailsRepositoryInterface extends BaseRepositoryInterface
{
    public function getActiveDate($schedule_id);
    public function getDepartmentInfo($schedule_id);
    public function getContent($call_id);
    public function getDetailDocument($detail_id);
    public function getDateTimeSetting($schedule_id);
    public function getAccompanyingUserDateTime($visit_id);
    public function getTitleOffice();

    public function saveSchedule($schedule_id, $facility_short_name, $visit_id, $start_date, $start_time, $end_time, $all_day_flag,$title,$display_option_type);
    public function getNumberPerson($schedule_id);
    public function getTitlePerson($schedule_id);
    public function getVisit($schedule_id, $visit_id);
    // public function getCall($visit_id);
    public function updateScheduleTitle($schedule_id,$title,$display_option_type);


    public function saveVisit($visit_id,$schedule_id,$user_name,$org_cd,$org_short_name,$important_flag,$remarks);
    public function saveAccompanyingUser($visit_id,$user_cd,$user_name,$org_cd,$org_short_name);
    public function checkAccompanyingUser($visit_id,$user_cd);
    public function getAccompanyingUser($visit_id);
    public function deleteAccompanyingUser($visit_id);

    public function deleteAllInterviewContent($visit_id , $call_id);

    // delete schedule
    public function deleteSchedule($schedule_id);

    public function getTCall($visit_id);
    public function deleteDetailDocument($detail_id);


    //InternalSchedule
    public function getInternalSchedule($schedule_id);
    public function updateInternalSchedule($schedule_id,$title,$office_schedule_type,$start_date,$start_time,$end_time,$all_day_flag);
    public function updateOfficeSchedule($schedule_id,$title,$office_schedule_type,$remarks);
    public function deleteInternalSchedule($schedule_id,$office_schedule_id);

    //PrivateSchedule
    public function getPrivateSchedule($schedule_id);
    public function updatePrivateSchedule($schedule_id,$title,$start_date,$start_time,$end_time,$all_day_flag);
    public function updatePrivate($schedule_id,$title,$remarks);
    public function deletePrivateSchedule($schedule_id);

    //add stock
    public function checkPersonCd($visit_id,$schedule_id,$person_cd);

    public function updateVisitAddStock($user_cd,$user_name,$org_cd,$org_short_name,$visit_id);
    public function getDetailOrder($call_id);
    public function insertDetail($call_id,$detail_order,$content_cd,$content_name,$product_cd,$product_name);
    public function insertDetailDocument($detail_id,$document_id);
    public function updateStatusStock($stock_id);

    public function getPosition($facility_cd,$person_cd);

    // ad interview
    public function getSchedule($schedule_id);
    public function getCallPerson($visit_id, $person_cd);
    public function updateCall($visit_id,$person_cd,$person_name,$department_cd,$department_name,$plan_flag);
    public function insertCall($visit_id,$person_cd,$person_name, $department_cd,$department_name,$plan_flag,$act_flag,$display_position_name);
    
    // copy plan
    public function createSchedule($t_schedule_temp,$data_user);
    public function getVisitCopyPlan($visit_id);
    public function createVisitCopyPlan($schedule_id,$t_visit,$data_user);


    public function getStatusReport($schedule_id);
    public function checkReportStartDate($start_date,$user_cd_login);
}
