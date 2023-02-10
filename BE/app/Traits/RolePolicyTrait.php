<?php
namespace App\Traits;

use App\Services\RolePolicyService;

trait RolePolicyTrait {
    // check role
    public function hasAnyRole($userCd, $roles)
    {
        $service = new RolePolicyService();
        return $service->hasAnyRole($userCd, $roles);
    }
    // check permission | permission : C , R , RA , U , UA , D , DA
    public function hasPermission($userCd, $screen, $permission)
    {
        $service = new RolePolicyService();
        return $service->hasPermission($userCd, $screen, $permission);
    }
    public function getRoleList($userCd)
    {
        $service = new RolePolicyService();
        return $service->getRoleList($userCd);
    }
}