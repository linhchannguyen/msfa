<?php

namespace App\Repositories\Region;

use App\Repositories\BaseRepositoryInterface;

interface RegionRepositoryInterface extends BaseRepositoryInterface
{
    public function allRegion();
    public function allPrefecture($data);
    public function allMunicipality($data,$name);
}
