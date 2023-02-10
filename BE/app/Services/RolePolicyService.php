<?php

namespace App\Services;

use App\Repositories\RolePolicy\RolePolicyRepository;
use App\Policies\Policy;

class RolePolicyService
{
    private $repo;

    public function __construct()
    {
        $this->repo = new RolePolicyRepository();
        $this->policy = new Policy();
    }

    public function getRoleList($userCd)
    {
        $roles = $this->repo->getRoleList($userCd);
        return $this->repo->_pluck($roles, 'role');
    }

    public function hasAnyRole($userCd, array $roles)
    {
        $userRoles = $this->getRoleList($userCd);
        $lackRoles = array_diff($roles, $userRoles);
        return count($lackRoles) < count($roles);
    }

    public function hasPermission($userCd, $screen, $permission)
    {

        if(!in_array($permission, ['C', 'R', 'RA', 'U', 'UA', 'D', 'DA'])) {
            return false;
        }

        $permissions = $this->getPermissionList($userCd);

        if(!isset($permissions[$screen])) {
            return false;
        }

        return in_array($permission, array_keys($permissions[$screen]['permissions']));
    }

    public function getPermissionList($userCd)
    {
        // Get roles of user
        $roles = $this->getRoleList($userCd);
        return $this->getScreenMenu($roles);
    }

    public function getScreenMenu($roles)
    {
        $getScreen = $this->repo->getScreen($roles);
        $getMenu = $this->repo->getMenu($roles);
        foreach($getMenu as &$value) {
            $value->screen = json_decode($value->screen);
        }
        return[
           'screen' => $getScreen,
           'menu' => $getMenu,
        ];
    }
}
