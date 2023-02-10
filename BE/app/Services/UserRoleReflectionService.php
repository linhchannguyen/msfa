<?php

namespace App\Services;

use App\Repositories\RoleUser\RoleUserRepositoryInterface;

use Illuminate\Console\Command;
use App\Services\LogService;

class UserRoleReflectionService
{
    private $repo, $system;
    protected $serviceAccessLog;

    public function __construct(RoleUserRepositoryInterface $repo, SystemParameterService $system, LogService $serviceAccessLog)
    {
        $this->repo = $repo;
        $this->system = $system;
        $this->serviceAccessLog = $serviceAccessLog;
    }

    public function getSystemParameterDate()
    {
        return $this->repo->getTimeSystem();
    }
    // Use only for batch job
    public function updateRoleUser($inputDate, $jobId, $keyBatchJob)
    {
        //delete role user
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "delete m_user_role data successfully");
        $this->repo->deleteRoleUser($jobId, $keyBatchJob);
        // update role user
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Insert successfully  i_user_role data into m_user_role");
        $this->repo->updateRoleUser($inputDate, $jobId, $keyBatchJob);
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  reflecting  m_user_role data log");
        return Command::SUCCESS;
    }
}
