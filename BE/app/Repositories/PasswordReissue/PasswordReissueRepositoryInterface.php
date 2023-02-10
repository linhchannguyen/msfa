<?php

namespace App\Repositories\PasswordReissue;

use App\Repositories\BaseRepositoryInterface;

interface PasswordReissueRepositoryInterface extends BaseRepositoryInterface
{
    public function getPasswordReissue($password_change,$user_cd,$user_name,$org_cd,$limit,$offset);
    public function updatePasswordReset($user_cd,$pass_word);
    public function getContentEmail($parameter_key);
    public function getInfoUser($user_cd);
    public function getInfoUserResult($user_cd);
}
