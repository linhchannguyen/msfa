<?php

namespace App\Repositories\ConditionArea;

use App\Repositories\BaseRepositoryInterface;

interface ConditionAreaRepositoryInterface extends BaseRepositoryInterface
{
    public function allSelectFacilityGroup($user_cd);
    public function allSelectPersonGroup($user_cd);
    public function allTargetProduct();
    public function allFacilityCategory();
    public function filterListFacility($data);
    public function filterRelason($facility);
}
