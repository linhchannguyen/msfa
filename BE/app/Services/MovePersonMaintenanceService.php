<?php

namespace App\Services;

use App\Repositories\MovePersonMaintenance\MovePersonMaintenanceRepositoryInterface;

use Illuminate\Console\Command;
use App\Services\LogService;

class MovePersonMaintenanceService
{
    private $repo;

    public function __construct(MovePersonMaintenanceRepositoryInterface $repo, LogService $serviceAccessLog)
    {
        $this->repo = $repo;
        $this->serviceAccessLog = $serviceAccessLog;
    }

    // Use only for batch job
    public function updateTransferDoctor($input_date, $jobId, $keyBatchJob)
    {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Moving Period");
        //get c_system_parameter where 異動判定期間
        $system_parameter = $this->repo->getSystemParameter();
        
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Reset move_flag");
        //update Master Facility Person
        $this->repo->updateMasterFacilityPerson($jobId, $keyBatchJob);

        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Set move_flag again");
        //insert History Facility Person
        $this->repo->getHistoryFacilityPerson($input_date,$system_parameter->parameter_value ?? null, $jobId, $keyBatchJob);
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End moving facility person log");

        return Command::SUCCESS;
    }
}
