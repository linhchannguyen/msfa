<?php

namespace App\Repositories\VariableDefinition;

use App\Repositories\BaseRepositoryInterface;

interface VariableDefinitionRepositoryInterface extends BaseRepositoryInterface
{
    public function getVariableByDefinitionNameAndLabel($definition_name,$definition_label);
    public function getVariableStatusTypeApprovalRequest($definition_label);
}
