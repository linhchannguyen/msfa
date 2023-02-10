<?php

namespace App\Repositories\CustomMaterialManagement;

use App\Repositories\BaseRepositoryInterface;

interface CustomMaterialManagementRepositoryInterface extends BaseRepositoryInterface
{
    public function getMedicalSubjectsGroup();
    public function getVariableDefinition();
    public function searchCustomMaterial($conditions);
}
