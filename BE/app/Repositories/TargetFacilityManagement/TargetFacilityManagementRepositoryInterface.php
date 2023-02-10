<?php

namespace App\Repositories\TargetFacilityManagement;

use App\Repositories\BaseRepositoryInterface;

interface TargetFacilityManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function getScreenData($date_system);
    public function getFacility($user_cd, $order_value, $limit, $offset);
    public function getTargetProduct($facility_cd, $order_value, $target_product_cd);
}
