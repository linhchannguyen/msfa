<?php

namespace App\Repositories\MovePersonMaintenance;

use App\Repositories\BaseRepositoryInterface;

interface MovePersonMaintenanceRepositoryInterface extends BaseRepositoryInterface
{
    //get c_system_parameter where 異動判定期間
    public function getSystemParameter();
    //update master Facility Person
    public function updateMasterFacilityPerson($jobId, $keyBatchJob);
    //get History Facility Person
    public function getHistoryFacilityPerson($input_date, $parameter_value, $jobId, $keyBatchJob);
}
