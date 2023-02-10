<?php

namespace App\Services;

use App\Repositories\PostedUserManagement\PostedUserManagementRepositoryInterface;

class PostedUserManagementService
{
    private $repo;

    public function __construct(PostedUserManagementRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function allPostingProhibited()
    {
        return $this->repo->allPostingProhibited();
    }

    public function getUnsuitableReport($user_cd)
    {
        return $this->repo->getUnsuitableReport($user_cd);
    }

    public function cancelPostingProhibited($user_cd)
    {
        return $this->repo->cancelPostingProhibited($user_cd);
    }
}
