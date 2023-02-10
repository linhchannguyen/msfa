<?php

namespace App\Services;

use App\Repositories\FacilityGroupDetail\FacilityGroupDetailRepositoryInterface;

use Illuminate\Console\Command;

class SelectFacilityGroupDetailService
{
    private $repo;

    public function __construct(FacilityGroupDetailRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    // Use only for batch job
    public function deleteFacilityGroupDetail($jobId, $keyBatchJob)
    {
        // Delete t_select_facility_group_detail where facility_cd not m_facility
        $this->repo->deleteFacilityGroupDetail($jobId, $keyBatchJob);
        return Command::SUCCESS;
    } 
}
