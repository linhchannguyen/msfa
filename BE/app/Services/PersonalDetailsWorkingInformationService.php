<?php

namespace App\Services;

use App\Repositories\PersonalDetailsWorkingInformation\PersonalDetailsWorkingInformationRepositoryInterface;
use App\Services\SystemParameterService;
use App\Traits\ArrayTrait;

class PersonalDetailsWorkingInformationService
{
    use ArrayTrait;
    private $repo;
    private $systemParameterService;

    public function __construct(PersonalDetailsWorkingInformationRepositoryInterface $repo ,SystemParameterService $systemParameterService)
    {
        $this->repo = $repo;
        $this->systemParameterService = $systemParameterService;
    }

    public function getScreenData()
    {
        $date_system = date_create($this->systemParameterService->getCurrentSystemDateTime())->format('Y-m-d');
        $data['phase_name'] = $this->repo->getPhaseName($date_system);
        return $data;
    }

    public function getWorkingInformation($person_cd,$limit,$offset)
    {
        $data = $this->repo->getWorkingInformation($person_cd,$limit,$offset);
        if(count($data['records']) > 0){
            foreach($data['records'] as $Item){
                $Item->segment = !empty($Item->segment) ? json_decode($Item->segment) : [];
                $Item->item = !empty($Item->item) ? json_decode($Item->item) : [];
            }
        }
        return $data;
    }
}