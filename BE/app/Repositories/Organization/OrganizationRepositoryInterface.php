<?php

namespace App\Repositories\Organization;

use App\Repositories\BaseRepositoryInterface;

interface OrganizationRepositoryInterface extends BaseRepositoryInterface
{
    public function allOrganizationLayer1($layer_num);
    public function allOrganizationLayer2($layer_num, $layer_1);
    public function allOrganizationLayer3($layer_num, $layer_1, $layer_2);
    public function allOrganizationLayer4($layer_num, $layer_1, $layer_2, $layer_3);
    public function allOrganizationLayer5($layer_num, $layer_1, $layer_2, $layer_3, $layer_4);
    public function allTitleOrg($title);
    public function allUser($data);
    public function getMainOrganizationsByUser($user_cd);
    public function getOrg($org_cd);
}