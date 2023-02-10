<?php

namespace App\Repositories\Calendar;

use App\Repositories\BaseRepository;
use App\Repositories\Calendar\CalendarRepositoryInterface;

class CalendarRepository extends BaseRepository implements CalendarRepositoryInterface
{
    protected $useAutoMeta = false;

    public function getList($startDate, $endDate)
    {
        $sql = "
        SELECT
            calendar_date,
            holiday_flag
        FROM
             m_calendar
        WHERE
            calendar_date >= :startDate and
            calendar_date <= :endDate
        ORDER BY calendar_date ASC
            ;
    ";
        return $this->_find($sql, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
}
