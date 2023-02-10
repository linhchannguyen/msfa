<?php

namespace App\Repositories\PostedUserManagement;

use App\Repositories\BaseRepositoryInterface;

interface PostedUserManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function allPostingProhibited();
    public function getUnsuitableReport($user_cd);
    public function cancelPostingProhibited($user_cd);
}