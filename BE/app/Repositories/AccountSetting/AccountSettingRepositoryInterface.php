<?php

namespace App\Repositories\AccountSetting;

use App\Repositories\BaseRepositoryInterface;

interface AccountSettingRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserInfo($userCd);
    public function getAccountPoint($userCd, $pointTargetType, $limit, $offset);
    public function getPointTargetType();
    public function changeAccountInfo($userCd, $data);
    public function findAccount($userCd);
    public function updateAvatar($userCd, $avatarId);
    public function findOrg($userCd);
}
