<?php

namespace App\Extensions;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Services\AuthService;
use App\Services\DevelopService;

class MUserContract  implements UserProvider
{
    public function retrieveById($identifier)
    {

    }
    public function retrieveByToken($identifier, $token)
    {

    }
    public function updateRememberToken(Authenticatable $user, $token)
    {

    }


    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */


    public function retrieveByCredentials(array $credentials)
    {
        if(isset($credentials['is_user']) && $credentials['is_user']){
            $LoginService =  new AuthService();
        }elseif(isset($credentials['is_develop']) && $credentials['is_develop']){
            $LoginService =  new DevelopService();
        }
        // validate
        return $LoginService;
    }

    public function validateCredentials($user, array $credentials)
    {
        if($credentials['is_proxy'] ?? false){
            return true;
        }else{
            $userData = $user->login($credentials['user_cd']);
            //logic to validate user
            $plain = $credentials['password_hash'];
            return Hash::check($plain, $userData->password ?? null );
        }
        
    }
}
