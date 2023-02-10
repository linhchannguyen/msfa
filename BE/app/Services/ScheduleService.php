<?php

namespace App\Services;

use App\Traits\AuthTrait;
use App\Traits\StatusReportTrait;
use App\Traits\DateTimeTrait;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Repositories\ConditionArea\ConditionAreaRepositoryInterface;
use App\Repositories\Auth\AuthRepository;

class ScheduleService
{
    use DateTimeTrait, StatusReportTrait, AuthTrait;
    private $repo, $area, $auth;

    public function __construct(ScheduleRepositoryInterface $repo, ConditionAreaRepositoryInterface $area, AuthRepository $auth)
    {
        $this->repo = $repo;
        $this->area = $area;
        $this->auth = $auth;
    }

    public function getScreenData()
    {
        $result['target_product'] = $this->area->allTargetProduct();
        $result['facility_person_segment_type'] = $this->repo->allFacilityPersonSegmentType();
        $result['display_option'] = $this->repo->selectDisplayOption();
        return $result;
    }

    public function getScheduleListByUserLogin($conditions)
    {
        return $this->repo->getScheduleListByUserLogin($conditions);
    }

    public function getVisitByScheduleId($schedule_id)
    {
        return $this->repo->getVisitByScheduleId($schedule_id);
    }

    public function getCallByVisitId($visit_id)
    {
        return $this->repo->getCallByVisitId($visit_id);
    }

    public function getVisitList($conditions)
    {
        return $this->repo->getVisitList($conditions);
    }

    public function addSchedule($data)
    {
        return $this->repo->addSchedule($data);
    }

    public function addVisit($data)
    {
        // 申し訳ございません、[訪問]に「ユーザ役職区分名」が漏れております。
        // タイムラインでは当時のユーザ役職情報も表示したいので、[訪問]に当時のユーザ役職区分・ユーザ役職区分名を保持できるようにしてください。
        // ※左記、[タイムライン実施者．ユーザ役職区分名] = [訪問．表示役職役職名（入力者当時）]は誤りで、正しくは[タイムライン実施者．ユーザ役職区分名] = [訪問．ユーザ役職区分名（入力者当時）]です。
        $user_cd =  $this->getCurrentUser();
        $user = $this->auth->getInfoUser($user_cd);
        $data['implement_user_post'] = $user->implement_user_post ?? "";
        $data['implement_user_post_name'] = $user->implement_user_post_name ?? "";
        return $this->repo->addVisit($data);
    }

    public function addCall($data)
    {
        return $this->repo->addCall($data);
    }

    public function addDetail($data)
    {
        return $this->repo->addDetail($data);
    }

    public function addDetailDocument($data)
    {
        return $this->repo->addDetailDocument($data);
    }

    public function lastInsertedSchedule()
    {
        return $this->repo->lastInsertedSchedule();
    }

    public function lastInsertedVisit()
    {
        return $this->repo->lastInsertedVisit();
    }

    public function lastInsertedCall()
    {
        return $this->repo->lastInsertedCall();
    }

    public function lastInsertedDetail()
    {
        return $this->repo->lastInsertedDetail();
    }

    public function getStockList($conditions)
    {
        return $this->repo->getStockList($conditions);
    }

    public function selectFacilityGroup($user_cd)
    {
        return $this->repo->selectFacilityGroup($user_cd);
    }

    public function selectPersonGroup($user_cd)
    {
        return $this->repo->selectPersonGroup($user_cd);
    }

    public function getFacilityGroupList($select_facility_group_id)
    {
        return $this->repo->getFacilityGroupList($select_facility_group_id);
    }

    public function getPersonGroupList($select_person_group_id)
    {
        return $this->repo->getPersonGroupList($select_person_group_id);
    }

    public function updateSchedule($data)
    {
        return $this->repo->updateSchedule($data);
    }

    public function reportStatus($schedule_id, $start_date)
    {
        $schedule = $this->repo->getStatusReport($schedule_id, $start_date);
        $report_id = $schedule->report_id ?? null;
        $status_report = $this->getStatusTypeReport($report_id);
        $number_status = $this->getNumberStatusReport($status_report);
        return $number_status;
    }

    public function reportStatusListAdd($conditions)
    {
        $schedule_type = $conditions['schedule_type'];
        unset($conditions['schedule_type']);
        $schedules = $this->repo->getStatusReportListAdd($conditions);
        foreach($schedules as $schedule){
            $report_id = $schedule->report_id ?? null;
            $status_report = $this->getStatusTypeReport($report_id);
            $report_status = $this->getNumberStatusReport($status_report);
            if (($schedule_type == SCHEDULE_DIVISION_CALL || $schedule_type == SCHEDULE_DIVISION_IN_HOUSE_SCHEDULE || $schedule_type == SCHEDULE_DIVISION_BRIEFING) 
                && in_array($report_status, [REPORT_STATUS_VALUE_SUBMITTED, REPORT_STATUS_VALUE_APPROVED])) {
                return  false;
            }
        }
        return true;
    }

    public function reportStatusListEdit($conditions)
    {
        $schedule_type = $conditions['schedule_type'];
        unset($conditions['schedule_type']);
        $schedules = $this->repo->getStatusReportListEdit($conditions);
        foreach($schedules as $schedule){
            $report_id = $schedule->report_id ?? null;
            $status_report = $this->getStatusTypeReport($report_id);
            $report_status = $this->getNumberStatusReport($status_report);
            if (($schedule_type == SCHEDULE_DIVISION_CALL || $schedule_type == SCHEDULE_DIVISION_IN_HOUSE_SCHEDULE || $schedule_type == SCHEDULE_DIVISION_BRIEFING) 
                && in_array($report_status, [REPORT_STATUS_VALUE_SUBMITTED, REPORT_STATUS_VALUE_APPROVED])) {
                return  false;
            }
        }
        return true;
    }

    public function updateScheduleTitle($data)
    {
        return $this->repo->updateScheduleTitle($data);
    }

    public function addOfficeSchedule($data)
    {
        return $this->repo->addOfficeSchedule($data);
    }

    public function addPrivate($data)
    {
        return $this->repo->addPrivate($data);
    }

    public function getScheduleById($conditions)
    {
        return $this->repo->getScheduleById($conditions);
    }

    public function getBriefingStatusFromSchedule($schedule_id)
    {
        return $this->repo->getBriefingStatusFromSchedule($schedule_id);
    }

    public function getConventionStatusFromSchedule($schedule_id)
    {
        return $this->repo->getConventionStatusFromSchedule($schedule_id);
    }

    public function checkCallUsageInSchedule($schedule_id)
    {
        return $this->repo->checkCallUsageInSchedule($schedule_id);
    }
}
