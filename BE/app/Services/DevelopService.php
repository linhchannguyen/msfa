<?php

namespace App\Services;

use App\Repositories\Develop\DevelopRepository;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class DevelopService extends Authenticatable implements JWTSubject
{
    public $sub;

    public function login($data)
    {
        $this->sub = $data;
        $login_data = (new DevelopRepository())->login($data);
         return count((array)$login_data) > 0 ? $login_data[0] : [];
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
