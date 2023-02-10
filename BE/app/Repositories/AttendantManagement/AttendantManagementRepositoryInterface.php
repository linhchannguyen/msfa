<?php

namespace App\Repositories\AttendantManagement;

use App\Repositories\BaseRepositoryInterface;

interface AttendantManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function getInfoConvention($convention_id);
    public function getParameterAddStock();
    public function getOtherAttendee($convention_id);
    public function getConventionStatusItem();
    public function getInputDeadline();
    public function getVariableDefinitionD14();
    public function getVariableDefinitionD18();
    public function getMedicalStaff();
    public function lastInsertedConventionAttendee();
    public function deleteHeadcount($convention_id);
    public function addHeadcount($data);
    public function addConventionAttendee($data);
    public function deleteConvention($convention_attendee_ids);
    public function updateConventionAttendee($data);
    public function addStatusDetail($data);
    public function updateStatusDetail($data);
    public function getStartDateConventionSchedule($convention_id);
    public function searchData($conditions);
    public function getLectureFollowUp();
    public function getConventionAttende($convention_attendee_id, $person_cd);
    public function getConventionAttendeeStatusDetail($convention_attendee_id);
}