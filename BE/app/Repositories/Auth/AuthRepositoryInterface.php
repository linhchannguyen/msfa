<?php

namespace App\Repositories\Auth;

use App\Repositories\BaseRepositoryInterface;

interface AuthRepositoryInterface extends BaseRepositoryInterface
{
    public function login($user_cd);
    public function changePassword($user_cd,$new_password);
    public function getInfoUser($user_cd);
    public function findOrg($userCd);
}
