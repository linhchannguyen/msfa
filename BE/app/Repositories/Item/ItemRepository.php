<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepository;
use App\Repositories\Item\ItemRepositoryInterface;
use App\Traits\DateTimeTrait;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    use DateTimeTrait;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function allVariable()
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = '品目カテゴリ区分'";
        return $this->_all($sql);
    }

    public function allProductGroup()
    {
        $sql = "SELECT product_group_cd, product_group_name FROM m_product_group";
        return $this->_all($sql);
    }

    public function allProductSelectFilter($data)
    {
        $sql = "SELECT T1.product_cd, T1.product_group_cd, T1.product_name
                FROM m_product T1 ";
        $where = " WHERE T1.start_date <= :start_date AND T1.end_date >= :end_date";
        $query['start_date'] = $data->date;
        $query['end_date'] = $data->date;
        $join = "";
        $product_group = [];
        if ($data->product_group_cd) {
            $where .= " AND T1.product_group_cd = :product_group_cd";
            $query['product_group_cd'] = $data->product_group_cd;
        }
        if ($data->definition_value == 10) {
            $product_group = $this->_find($sql . $where, $query);
        } else if ($data->definition_value == 20) {
            $join = " JOIN t_select_product T2 ON T1.product_cd = T2.product_cd ";
            $product_group = $this->_find($sql . $join . $where, $query);
        } else if ($data->definition_value == 30) {
            $join = " JOIN m_target_product_map T2 ON T1.product_cd = T2.product_cd 
                    JOIN m_target_product T3 ON T3.target_product_cd = T2.target_product_cd ";
            $product_group = $this->_find($sql . $join . $where, $query);
        }
        return $product_group;
    }
}
