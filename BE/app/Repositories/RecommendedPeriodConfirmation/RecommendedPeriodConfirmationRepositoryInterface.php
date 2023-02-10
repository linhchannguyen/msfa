<?php

namespace App\Repositories\RecommendedPeriodConfirmation;

use App\Repositories\BaseRepositoryInterface;

interface RecommendedPeriodConfirmationRepositoryInterface extends BaseRepositoryInterface
{
    public function getDataUser($user_cd, $user_name, $org_cd, $date, $limit, $offset);
    public function getUserInformation($user_cd_list, $user_name, $org_cd, $date);
    public function getOrganizationInformation($user_cd_list,$org_cd, $date);
    public function getRoleInformation($user_cd_list,$date);
    public function getApproval($user_cd_list,$date,$approval_type);
}
