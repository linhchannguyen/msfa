<?php

namespace App\Repositories\TargetSelectionPeriodManagement;

use App\Repositories\BaseRepositoryInterface;

interface TargetSelectionPeriodManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function getTargetSelectionPeriodManagement($date_system);
    public function saveTargetProduct($target_product_cd,$selection_start_date,$selection_end_date);
}
