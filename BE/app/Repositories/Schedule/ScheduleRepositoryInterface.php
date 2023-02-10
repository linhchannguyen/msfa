<?php

namespace App\Repositories\Schedule;

use App\Repositories\BaseRepositoryInterface;

interface ScheduleRepositoryInterface extends BaseRepositoryInterface
{
    public function getScheduleListByUserLogin($conditions);
    public function getVisitByScheduleId($schedule_id);
    public function getCallByVisitId($visit_id);
    public function allFacilityPersonSegmentType();
    public function getStockList($conditions);
    public function selectFacilityGroup($user_cd);
    public function selectPersonGroup($user_cd);
    public function selectDisplayOption();
    public function getFacilityGroupList($select_facility_group_id);
    public function getPersonGroupList($select_person_group_id);
    public function getVisitList($conditions);
    public function addSchedule($data);
    public function addVisit($data);
    public function addCall($data);
    public function addDetail($data);
    public function addDetailDocument($data);
    public function lastInsertedSchedule();
    public function lastInsertedVisit();
    public function lastInsertedCall();
    public function lastInsertedDetail();
    public function updateSchedule($data);
    public function updateScheduleConvention($data);
    public function updateScheduleTitle($data);
    public function addOfficeSchedule($data);
    public function addPrivate($data);
    public function getScheduleById($schedule_id);
    public function getStatusReport($schedule_id, $start_date = null);
    public function getStatusReportListAdd($conditions);
    public function getStatusReportListEdit($conditions);
    public function getBriefingStatusFromSchedule($schedule_id);
    public function getConventionStatusFromSchedule($schedule_id);
    public function checkCallUsageInSchedule($schedule_id);
}
