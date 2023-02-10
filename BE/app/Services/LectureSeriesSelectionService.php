<?php

namespace App\Services;

use App\Repositories\LectureSeriesSelection\LectureSeriesSelectionRepositoryInterface;

class LectureSeriesSelectionService
{
    private $repo;

    public function __construct(LectureSeriesSelectionRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getLectureSeriesSelection($product_cd, $convention_name, $series_flag, $limit, $offset)
    {
        return $this->repo->getLectureSeriesSelection($product_cd, $convention_name, $series_flag, $limit, $offset);
    }

    public function register($convention_id,$parent_series_flag,$series_convention_id)
    {
        if($parent_series_flag == 0 && empty($series_convention_id)){
            $parent_series_flag = 1;
            $series_convention_id  = $convention_id;
            $this->repo->register($convention_id,$parent_series_flag,$series_convention_id);
        }
        return true;
    }
}
