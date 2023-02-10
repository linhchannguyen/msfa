<?php

namespace App\Services;

use App\Repositories\Unapproved\UnapprovedRepositoryInterface;
use App\Services\OrganizationService;
use App\Traits\StatusReportTrait;
use App\Repositories\VariableDefinition\VariableDefinitionRepositoryInterface;

class UnapprovedService
{
    use StatusReportTrait;
    private $repo, $variable, $definition_label_report;

    public function __construct(
        UnapprovedRepositoryInterface $repo,
        VariableDefinitionRepositoryInterface $variable
    ) {
        $this->repo = $repo;
        $this->variable = $variable;
        $this->definition_label_report = "報告";
    }

    public function getData($request)
    {
        $request->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_report)->definition_value ?? null;
        $result['data'] = $this->repo->allData($request);
        $result['mode_week'] = $this->modeReport("報告");
        return $result;
    }
}
