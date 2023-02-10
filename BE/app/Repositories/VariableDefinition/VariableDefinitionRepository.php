<?php

namespace App\Repositories\VariableDefinition;

use App\Repositories\BaseRepository;
use App\Repositories\VariableDefinition\VariableDefinitionRepositoryInterface;
use App\Traits\DateTimeTrait;

class VariableDefinitionRepository extends BaseRepository implements VariableDefinitionRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = true;
    private $date, $approval_application_classification;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
        $this->approval_application_classification = "承認申請区分";
    }

    public function  getVariableByDefinitionNameAndLabel($definition_name, $definition_label)
    {
        $query['definition_name'] = $definition_name;
        $query['definition_label'] = $definition_label;
        $sql = "SELECT definition_value,definition_label,definition_name FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label";
        return $this->_first($sql, $query);
    }

    public function  getVariableStatusTypeApprovalRequest($definition_label)
    {
        $query['definition_name'] = $this->approval_application_classification;
        $query['definition_label'] = $definition_label;
        $sql = "SELECT definition_value,definition_label,definition_name FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label";
        return $this->_first($sql, $query);
    }
}
