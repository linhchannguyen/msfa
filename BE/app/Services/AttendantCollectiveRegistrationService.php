<?php

namespace App\Services;

use App\Repositories\AttendantCollectiveRegistration\AttendantCollectiveRegistrationRepositoryInterface;

class AttendantCollectiveRegistrationService
{
    private $repo;

    public function __construct(AttendantCollectiveRegistrationRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function import($data_import)
    {
        return $this->repo->import($data_import);
    }

    public function getFacilityCd($facility_list)
    {
        return $this->repo->getFacilityCd($facility_list);
    }

    public function getPersonCd($person_list)
    {
        return $this->repo->getPersonCd($person_list);
    }

    public function getFacilityPerson($facility_list,$person_list)
    {
        return $this->repo->getFacilityPerson($facility_list,$person_list);
    }

    public function getRegistration($facility_list,$person_list,$convention_id)
    {
        return $this->repo->getRegistration($facility_list,$person_list,$convention_id);
    }

    public function getRegistrationTemp($facility_list,$person_list,$convention_id)
    {
        return $this->repo->getRegistrationTemp($facility_list,$person_list,$convention_id);
    }
}
