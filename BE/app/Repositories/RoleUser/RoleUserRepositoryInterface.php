<?php

namespace App\Repositories\RoleUser;

use App\Repositories\BaseRepositoryInterface;

interface RoleUserRepositoryInterface extends BaseRepositoryInterface
{
    // get time system
    public function getTimeSystem();
    //delete role user
    public function deleteRoleUser($jobId, $keyBatchJob);
    // update role user
    public function updateRoleUser($dateSystem, $jobId, $keyBatchJob);
}
