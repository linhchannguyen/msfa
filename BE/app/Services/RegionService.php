<?php

namespace App\Services;

use App\Repositories\Region\RegionRepositoryInterface;

class RegionService
{
    private $repo;

    public function __construct(RegionRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getListData()
    {
        $region =  $this->repo->allRegion();
        array_unshift($region, array("region_cd" => "", "region_name" => "全て"));
        return $region;
    }

    public function getDataPrefecture($data)
    {
        $region_cd = $data->region_cd;
        $prefecture = $this->repo->allPrefecture($region_cd);
        return $prefecture;
    }

    public function getDataMunicipality($data)
    {
        $prefecture_cd = $data->prefecture_cd;
        $name = $data->municipality_name;
        return $this->repo->allMunicipality($prefecture_cd,$name);
    }
}
