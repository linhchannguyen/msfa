<?php

namespace App\Repositories\Material;

use App\Repositories\BaseRepositoryInterface;

interface MaterialRepositoryInterface extends BaseRepositoryInterface
{
    public function allMaterial();

    public function allMedicalSubjects();

    public function allSafetyInformation();

    public function allOffLabelInformation();

    public function allDataFilter($data);

    public function getOrgDocuemnt($org_cd);
}
