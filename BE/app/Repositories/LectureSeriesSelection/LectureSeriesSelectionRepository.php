<?php

namespace App\Repositories\LectureSeriesSelection;

use App\Repositories\BaseRepository;
use App\Repositories\LectureSeriesSelection\LectureSeriesSelectionRepositoryInterface;
use App\Traits\StringConvertTrait;

class LectureSeriesSelectionRepository extends BaseRepository implements LectureSeriesSelectionRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;
    
    public function getLectureSeriesSelection($product_cd, $convention_name, $series_flag, $limit, $offset)
    {
        $query = [];
        $sql = "
        SELECT
            t_convention.convention_id,
            t_convention.convention_name,
            t_convention.parent_series_flag,
            t_convention.series_convention_id
        FROM t_convention 
        LEFT JOIN t_convention_product
            ON t_convention.convention_id = t_convention_product.convention_id
        JOIN t_schedule 
            ON t_convention.schedule_id = t_schedule.schedule_id
        WHERE 1 = 1 ";

        if(!empty($product_cd)){
            $product_cd = explode(',',$product_cd);
            $sql .= " AND t_convention_product.product_cd IN " . $this->_buildCommaString($product_cd, true) . " ";
        }

        if(!empty($convention_name)){
            $sql .= " AND t_convention.convention_name LIKE :convention_name ";
            $query['convention_name'] = "%" . trim($this->convert_kana($convention_name)) . "%";
        }

        if($series_flag == 1){
            $sql .= " AND t_convention.parent_series_flag = :parent_series_flag ";
            $query['parent_series_flag'] = $series_flag;
        }

        $sql .= " GROUP BY t_convention.convention_id ORDER BY t_schedule.start_time DESC ,  DATE_FORMAT(t_schedule.start_time,'%H:%i:%s') DESC, t_convention.convention_id DESC ;";

        return $this->_paginate($sql, $query, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }
    
    public function register($convention_id,$parent_series_flag,$series_convention_id)
    {
        $sql = "UPDATE t_convention SET parent_series_flag = :parent_series_flag , series_convention_id = :series_convention_id WHERE convention_id = :convention_id ;";
        return $this->_update($sql,['convention_id' => $convention_id ,
         'series_convention_id' => $series_convention_id ,
         'parent_series_flag' => $parent_series_flag
        ]);
    }
}
