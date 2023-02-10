<?php

namespace App\Services;

use App\Repositories\Auth\AuthRepository;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AuthService extends Authenticatable implements JWTSubject
{
    private $sub , $auth;

    public function __construct()
    {
        $this->auth = new AuthRepository();
    }

    public function login($data)
    {
        $user_cd = $data;
        $this->sub = $data;
        return $this->auth->login($user_cd);
    }

    public function changePassword($user_cd,$new_password)
    {
        $this->auth->changePassword($user_cd,$new_password);
    }

    public function getInfoUser($user_cd)
    {
        return $this->auth->getInfoUser($user_cd);
    }

    public function getInfoUserLogin($user_cd)
    {
        return $this->auth->getInfoUserLogin($user_cd);
    }

    public function findOrg($userCd)
    {
        return $this->auth->findOrg($userCd);
    }

    public function getJWTIdentifier()
    {
        return $this->sub;
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }
}
