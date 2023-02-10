<?php

namespace App\Repositories\WatchUserColor;

use App\Repositories\BaseRepositoryInterface;

interface WatchUserColorRepositoryInterface extends BaseRepositoryInterface
{
    public function getVariableDefinition();
    public function getListByUser($startDate, $endDate, $user_cd,$type,$currentUser);
    public function getWatchUserList($currentUser);
    public function getList();
    public function check($userCd, $currentUser);
    public function create($data);
    public function update($userCd, $displayColorCd, $displayFlag, $currentUser);
    public function deleteWatchUser($currentUser, $user_cd);
}
