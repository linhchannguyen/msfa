<?php

namespace App\Repositories\ContactWholesalerMasterReflection;

use App\Repositories\BaseRepositoryInterface;

interface ContactWholesalerMasterReflectionRepositoryInterface extends BaseRepositoryInterface
{
    // delete Facility
    public function deleteFacility($jobId, $keyBatchJob);
    //add Facility
    public function addFacility($jobId, $keyBatchJob);
}
