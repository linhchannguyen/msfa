<?php

namespace App\Traits;

use App\Services\AuthService;
use App\Services\DevelopService;

trait AuthTrait
{
    public function getCurrentUser()
    {
        return config('global.jwt_sub');
    }

    public function getInfoCurrentUser($data, $user_login)
    {
        if ($user_login == 'is_user') {
            $AuthService = new AuthService();
        } elseif ($user_login == 'is_develop') {
            $AuthService = new DevelopService();
        }

        return $AuthService->login($data);
    }

    public function isProxyUser()
    {
        return !is_null(config('global.proxy_sub'));
    }

    public function getCurrentProxyUser()
    {
        return config('global.proxy_sub');
    }

    public function getOrgCurrentUser($user_cd)
    {
        $AuthService = new AuthService();
        return $AuthService->findOrg($user_cd);
    }
}
