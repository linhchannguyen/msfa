<?php

namespace App\Services;

use App\Repositories\ContactWholesalerMasterReflection\ContactWholesalerMasterReflectionRepositoryInterface;

use Illuminate\Console\Command;

class ContactWholesalerMasterReflectionService
{
    private $repo;

    public function __construct(ContactWholesalerMasterReflectionRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    // delete Facility
    public function deleteFacility($jobId, $keyBatchJob)
    {
        return $this->repo->deleteFacility($jobId, $keyBatchJob);
    }

    // add Facility
    public function addFacility($jobId, $keyBatchJob)
    {
        return $this->repo->addFacility($jobId, $keyBatchJob);
    }
}
