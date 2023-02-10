<?php

namespace App\Repositories\AttendantCollectiveRegistration;

use App\Repositories\BaseRepositoryInterface;

interface AttendantCollectiveRegistrationRepositoryInterface extends BaseRepositoryInterface
{
    public function import($data_import);
    public function getFacilityCd($facility_list);
    public function getPersonCd($person_list);
    public function getFacilityPerson($facility_list,$person_list);
    public function getRegistration($facility_list,$person_list,$convention_id);
    public function getRegistrationTemp($facility_list,$person_list,$convention_id);
}
