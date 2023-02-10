<?php

namespace App\Repositories\Region;

use App\Repositories\BaseRepository;
use App\Repositories\Region\RegionRepositoryInterface;
use App\Traits\StringConvertTrait;

class RegionRepository extends BaseRepository implements RegionRepositoryInterface
{
    use StringConvertTrait;
    public function allRegion()
    {
        $sql = "SELECT region_cd, region_name FROM m_region";
        return $this->_all($sql);
    }

    public function allPrefecture($region_cd)
    {
        $where = "";
        $query = [];
        if ($region_cd) {
            $where .= " WHERE T2.region_cd = :region_cd";
            $query['region_cd'] = $region_cd;
        }
        $sql = "SELECT prefecture_cd, prefecture_name FROM m_prefecture T1 join m_region T2 on T1.region_cd = T2.region_cd ";
        return $this->_find($sql . $where, $query);
    }

    public function allMunicipality($prefecture_cd, $name)
    {
        $where = "";
        $query['prefecture_cd'] = $prefecture_cd;
        if ($name) {
            $where .= " AND municipality_name like :region_cd";
            $query['region_cd'] = "%" . trim($this->convert_kana($name)) . "%";
        }
        $sql = "SELECT municipality_cd, municipality_name, municipality_name_kana FROM m_municipality WHERE prefecture_cd = :prefecture_cd";
        return $this->_find($sql . $where, $query);
    }
}
