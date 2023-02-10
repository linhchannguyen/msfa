<?php

namespace App\Repositories\FacilityGroup;

use App\Repositories\BaseRepositoryInterface;

interface FacilityGroupRepositoryInterface extends BaseRepositoryInterface
{
    public function allData($request);
    public function allFacility($select_facility_group_id);
    public function deleteFacilityGroup($select_facility_group_id);
    public function updateFacilityGroup($data);
    public function insertFacilityGroup($data);
    public function deleteFacility($facility_group_id);
    public function updateFacility($facility_group_id,$facility_cd,$user_cd);
}
