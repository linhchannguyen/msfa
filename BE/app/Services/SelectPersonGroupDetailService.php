<?php

namespace App\Services;

use App\Repositories\PersonGroupDetail\PersonGroupDetailRepositoryInterface;

use Illuminate\Console\Command;

class SelectPersonGroupDetailService
{
    private $repo;

    public function __construct(PersonGroupDetailRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    // Use only for batch job
    public function deletePersonGroupDetail($jobId, $keyBatchJob)
    {
        // Delete t_select_facility_group_detail where facility_cd not m_facility
        $person_group_detail = $this->repo->getPersonGroupDetail();
        foreach ($person_group_detail as $item) {
            $this->repo->deletePersonGroupDetail($item->facility_cd, $item->person_cd, $jobId, $keyBatchJob);
        }
        return Command::SUCCESS;
    }
}
