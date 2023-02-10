<?php

namespace App\Repositories\FacilityDetailsNotes;

use App\Repositories\BaseRepositoryInterface;

interface FacilityDetailsNotesRepositoryInterface extends BaseRepositoryInterface
{
    public function getScreenData();
    public function getFacilityDetailsNotes($facility_cd, $content, $start_date, $end_date, $consideration_type);
    public function saveConsiderationConfirm($facility_consideration_id, $confirmed_flag, $user_cd, $confirmed_datetime);
    public function deleteConsideration($facility_consideration_id);

    //ADD NEW
    public function saveFacilityConsiderationNewMode($facility_cd, $consideration_type, $consideration_contents, $user_cd_login, $user_name_login, $org_cd_login, $org_label_login, $data_system);
    public function getInformCategory();
    public function facilityConsiderationConfirmNewMode($facility_consideration, $user_cd, $confirmed_flag);
    public function facilityConsiderationConfirmUpdateMode($facility_consideration_id, $user_cd, $confirmed_flag);

    // UPDATE 
    public function updateFacilityConsideration($facility_cd, $facility_consideration_id, $consideration_type, $consideration_contents, $user_cd_login, $user_name_login, $org_cd_login, $org_label_login, $data_system);
    public function deleteFacilityConsiderationConfirm($facility_consideration_id, $user_cd);

    public function checkFacilityConsiderationConfirm($facility_consideration_id, $user_cd);
}
