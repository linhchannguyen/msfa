<?php

namespace App\Repositories\PermissionSetting;

use App\Repositories\BaseRepositoryInterface;

interface PermissionSettingRepositoryInterface extends BaseRepositoryInterface
{
    public function getRole();
    public function getDirector();
    public function getPermissionList($user_name,$user_cd,$org_cd,$reference_date,$director,$limit,$offset);
    public function getOrgCdList($user_cd,$org_cd,$reference_date,$director);
    public function getRoleList($user_cd,$reference_date);
    public function getRoleListFromCurrent($user_cd,$reference_date);
    public function deletePermission($user_cd,$start_date);
    public function updatePermission($data);
    public function updateMasterUserRole($data);
    public function getCurrenntPermission($user_cd,$start_date,$type);
    public function updatePermissionEndDate($user_cd,$start_date,$end_date);
}
