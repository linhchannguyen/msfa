<?php

namespace App\Services;

use App\Repositories\CustomMaterialManagement\CustomMaterialManagementRepositoryInterface;
use App\Traits\DateTimeTrait;

class CustomMaterialManagementService
{
    use DateTimeTrait;
    private $repo;

    public function __construct(CustomMaterialManagementRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getScreenData()
    {
        $result['medical_subjects_group'] = $this->repo->getMedicalSubjectsGroup();
        $result['variable_definition'] = $this->repo->getVariableDefinition();
        return $result;
    }

    public function searchCustomMaterial($conditions)
    {
        return $this->repo->searchCustomMaterial($conditions);
    }
}