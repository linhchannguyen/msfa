<?php

namespace App\Services;

use App\Repositories\SystemParameter\SystemParameterRepositoryInterface;

class SystemParameterService
{
    private $repo;

    public function __construct(SystemParameterRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getSystemTimezone()
    {
        $timezone = $this->repo->getTimezone();
        return $timezone->parameter_value ?? null;
    }

    public function getCurrentSystemDateTime()
    {
        $system_date = $this->getSystemTimezone();
        $timezone = $this->get_local_time();
        $date = new \DateTime("now", new \DateTimeZone($timezone));
        if (empty($system_date)) {
            return $this->getDateTime($date);
        }
        return $system_date . ' ' . $this->getTime($date);
    }

    public function getDateTime($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getTime($date)
    {
        return $date->format('H:i:s');
    }

    private function get_local_time()
    {
        return getTimezoneCurrentMachine();
    }
}
