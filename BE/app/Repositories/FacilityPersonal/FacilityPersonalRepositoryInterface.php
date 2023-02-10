<?php

namespace App\Repositories\FacilityPersonal;

use App\Repositories\BaseRepositoryInterface;

interface FacilityPersonalRepositoryInterface extends BaseRepositoryInterface
{
    public function allVariable($data);
    public function allSegment();
    public function allMedicalStaff();
    public function filterListFacility($data);
}
