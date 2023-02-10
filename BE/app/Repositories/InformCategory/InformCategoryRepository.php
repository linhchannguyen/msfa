<?php

namespace App\Repositories\InformCategory;

use App\Repositories\BaseRepository;
use App\Repositories\InformCategory\InformCategoryRepositoryInterface;

class InformCategoryRepository extends BaseRepository implements InformCategoryRepositoryInterface
{
    public function all()
    {
        $sql = "SELECT
            inform_category_cd,
            inform_category_name,
            1 as checked
        FROM
            m_inform_category
        ORDER BY
            sort_order ASC";

        return $this->_all($sql);
    }
}