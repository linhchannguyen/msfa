<?php

namespace App\Services;

use App\Repositories\UtilizationPointTotal\UtilizationPointTotalRepositoryInterface;

use Illuminate\Console\Command;

class UtilizationPointTotalService
{
    private $repo;

    public function __construct(UtilizationPointTotalRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    // Use only for batch job
    public function sumaryPoints($jobId, $keyBatchJob)
    {
        // Calculate sum of points from t_active_point table
        $points = $this->repo->sumaryPoints();
        $this->repo->deleteTotalPoints($jobId, $keyBatchJob);
        // Update or Insert s_active_point table
        foreach ($points as $point) {
            $this->repo->updateOrInsertTotalPoints($point,$jobId, $keyBatchJob);
        }
        return Command::SUCCESS;
    }
}
