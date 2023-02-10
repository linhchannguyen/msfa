<?php

namespace App\Repositories\LectureSeriesSelection;

use App\Repositories\BaseRepositoryInterface;

interface LectureSeriesSelectionRepositoryInterface extends BaseRepositoryInterface
{
    public function getLectureSeriesSelection($product_cd, $convention_name, $series_flag, $limit, $offset);
    public function register($convention_id,$parent_series_flag,$series_convention_id);
}
