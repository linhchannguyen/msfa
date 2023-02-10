<?php

namespace App\Repositories\UtilizationPointTotal;

use App\Repositories\BaseRepositoryInterface;

interface UtilizationPointTotalRepositoryInterface extends BaseRepositoryInterface
{
    public function sumaryPoints();
    public function deleteTotalPoints($jobId, $keyBatchJob);
    public function updateOrInsertTotalPoints($data, $jobId, $keyBatchJob);
}
