<?php

namespace App\Repositories\RolePolicy;

use App\Repositories\BaseRepositoryInterface;

interface RolePolicyRepositoryInterface extends BaseRepositoryInterface
{
    public function getRoleList($userCd);
    public function getMenu($roles);
    public function getScreen($roles);
}
