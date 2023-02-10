<?php

namespace App\Services;

use App\Repositories\Calendar\CalendarRepositoryInterface;
use App\Repositories\Auth\AuthRepository;

class CalendarService
{
    private $repo, $auth;

    public function __construct(CalendarRepositoryInterface $repo)
    {
        $this->repo = $repo;
        $this->auth = new AuthRepository();
    }

    public function getList($startDate, $endDate)
    {
        return $this->repo->getList($startDate, $endDate);
    }
    public function  getListByMonthYear($startDate, $endDate)
    {
        $data = $this->repo->getList($startDate, $endDate);
        foreach($data as &$value){
            $value->calendar_date = date("Y/m/d",strtotime($value->calendar_date));
        }
        return $data;
    }
}
