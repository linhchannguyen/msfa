<?php

namespace App\Traits;

use App\Repositories\SystemParameter\SystemParameterRepository;
use App\Services\SystemParameterService;

trait DateTimeTrait
{
    public function correctDateTimeFormat($date, $outputFormat = 'Y-m-d H:i:s')
    {
        $compareFormat = str_replace('-', '/', $outputFormat);
        if (!$this->isValidDateTime($date, $compareFormat)) {
            return !empty($date) ? date($outputFormat, strtotime($date)) : $date;
        }

        $d = new \DateTime($date);
        return $d->format($outputFormat);
    }

    public function isValidDateTime($date, $compareFormat = 'Y-m-d H:i:s')
    {
        $d = \DateTime::createFromFormat($compareFormat, $date);
        return $d && $d->format($compareFormat) === $date;
    }

    public function currentJapanDateTime($outputFormat = "Y-m-d H:i:s")
    {
        $systemParameterService = new SystemParameterService(new SystemParameterRepository());
        return date($outputFormat, strtotime($systemParameterService->getCurrentSystemDateTime()));
    }

    public function responseDateTimeFormat($date, $outputFormat = "Y/m/d H:i:s")
    {
        $d = new \DateTime($date);
        return $d->format($outputFormat);
    }

    public function getEra($array, $era_year, &$graduation_year)
    {
        foreach ($array as $era) {
            if (substr($era_year, 0, 1) == $era->definition_value) {
                $era->definition_label = $era->definition_label ?? "";
                $graduation_year = $era->definition_label . substr($era_year, 1, 2);
            }
        }
    }
}
