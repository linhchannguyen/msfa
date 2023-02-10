<?php

namespace App\Services;

use App\Repositories\TargetSelectionPeriodManagement\TargetSelectionPeriodManagementRepositoryInterface;

class TargetSelectionPeriodManagementService
{
    private $repo;

    public function __construct(TargetSelectionPeriodManagementRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getTargetSelectionPeriodManagement($date_system)
    {
        return $this->repo->getTargetSelectionPeriodManagement($date_system);
    }

    public function saveTargetProduct($target_product_cd,$selection_start_date,$selection_end_date)
    {
        return $this->repo->saveTargetProduct($target_product_cd,$selection_start_date,$selection_end_date);
    }
}
