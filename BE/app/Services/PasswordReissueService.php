<?php

namespace App\Services;

use App\Repositories\PasswordReissue\PasswordReissueRepositoryInterface;
use Carbon\Carbon as time;
use App\Services\SystemParameterService;

class PasswordReissueService
{
    private $repo;

    public function __construct(PasswordReissueRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getPasswordReissue($user_cd,$user_name,$password_change,$org_cd,$limit,$offset)
    {
        return $this->repo->getPasswordReissue($password_change,$user_cd,$user_name,$org_cd,$limit,$offset);
    }

    public function getInfoUserResult($user_cd)
    {
        return $this->repo->getInfoUserResult($user_cd);
    }

    public function updatePasswordReset($user_cd,$pass_word)
    {
        $this->repo->updatePasswordReset($user_cd,$pass_word);
    }

    public function getInfoSendEmail($user_cd)
    {
        $data['user'] = $this->repo->getInfoUser($user_cd);
        $data['email'] = $this->repo->getContentEmail('システム名');
        $data['mailto'] = $this->repo->getContentEmail('問合せ先');
        return $data;
    }
}
