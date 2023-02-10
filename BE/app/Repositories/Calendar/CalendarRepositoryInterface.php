<?php

namespace App\Repositories\Calendar;

use App\Repositories\BaseRepositoryInterface;

interface CalendarRepositoryInterface extends BaseRepositoryInterface
{
    public function getList($startDate, $endDate);
    // public function getListByMonthYear($startDate, $endDate);
}
