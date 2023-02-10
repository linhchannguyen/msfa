<?php

namespace App\Services;

use App\Repositories\WatchUserColor\WatchUserColorRepositoryInterface;
use App\Repositories\Auth\AuthRepository;
use App\Traits\DateTimeTrait;

class WatchUserColorService
{
    use DateTimeTrait;
    private $repo, $auth;

    public function __construct(WatchUserColorRepositoryInterface $repo)
    {
        $this->repo = $repo;
        $this->auth = new AuthRepository();
    }

    public function getVariableDefinition()
    {
        $result['variable_definition'] = $this->repo->getVariableDefinition();
        $result['watch_user_color'] = $this->repo->getList();
        return $result;
    }

    public function getListByUser($startDate, $endDate, $user_cd, $type, $currentUser)
    {
        $data =  $this->repo->getListByUser($startDate, $endDate, $user_cd, $type, $currentUser);
        foreach ($data as &$value) {
            $value->start_date =  $this->responseDateTimeFormat($value->start_date, 'Y/m/d');
            $value->start_time =  $this->responseDateTimeFormat($value->start_time);
            $value->end_time =  $this->responseDateTimeFormat($value->end_time);
        }
        return $data;
    }

    public function getWatchUserList($currentUser)
    {
        $watch_colors = $this->repo->getList();
        $datas = $this->repo->getWatchUserList($currentUser);
        foreach($datas as $values){
            if(empty($values->display_color_cd)){
                if(!empty($watch_colors)){
                    $values->display_color_cd = $watch_colors[0]->display_color_cd;
                    $values->display_color = $watch_colors[0]->display_color;
                }else{
                    $values->display_color_cd = 0;
                    $values->display_color = 0;
                }
            }
        }
        return $datas;
    }

    public function deleteWatchUser($currentUser, $user_cd)
    {
        return $this->repo->deleteWatchUser($currentUser, $user_cd);
    }

    public function changeColorUser($userCd, $displayColorCd, $displayFlag, $currentUser)
    {
        $result = false;
        $datas = [];
        foreach ($userCd as $key => $user_cd) {
            array_push(
                $datas,
                [
                    "user_cd" => $currentUser,
                    "watch_user_cd" => $user_cd,
                    "display_flag" => $displayFlag ?? 0,
                    "display_color_cd" => $displayColorCd[$key]
                ]
            );
        }
        if (count($userCd) == 1) {
            $check = $this->repo->check($userCd[0], $currentUser);
            if (!empty($check)) {
                $result = $this->repo->update($userCd[0], $displayColorCd[0], $displayFlag, $currentUser);
            }else{
                $result = $this->repo->create($datas);
            }
        }else{
            $result = $this->repo->create($datas);
        }
        return $result;
    }
}
