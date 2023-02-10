<?php

namespace App\Repositories\Stock;

use App\Repositories\BaseRepository;
use App\Repositories\Stock\StockRepositoryInterface;
use App\Traits\StringConvertTrait;

class StockRepository extends BaseRepository implements StockRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;

    public function search($conditions, $limit, $offset)
    {
        $condition_values = [];
        $sql = "SELECT
            t_stock.stock_id,
            (SELECT GROUP_CONCAT(m_product.product_cd) FROM t_stock_product JOIN m_product ON m_product.product_cd = t_stock_product.product_cd WHERE t_stock_product.stock_id = t_stock.stock_id) AS product_cd_list,
            (SELECT GROUP_CONCAT(m_product.product_name) FROM t_stock_product JOIN m_product ON m_product.product_cd = t_stock_product.product_cd WHERE t_stock_product.stock_id = t_stock.stock_id) AS product_name_list,
            (SELECT GROUP_CONCAT(t_document.document_id) FROM t_stock_document JOIN t_document ON t_document.document_id = t_stock_document.document_id WHERE t_stock_document.stock_id = t_stock.stock_id) AS document_id_list,
            (SELECT GROUP_CONCAT(t_document.document_name) FROM t_stock_document JOIN t_document ON t_document.document_id = t_stock_document.document_id WHERE t_stock_document.stock_id = t_stock.stock_id) AS document_name_list,
            (SELECT GROUP_CONCAT(t_document.file_type) FROM t_stock_document JOIN t_document ON t_document.document_id = t_stock_document.document_id WHERE t_stock_document.stock_id = t_stock.stock_id) AS file_type_list,
            (SELECT GROUP_CONCAT(t_document.document_type) FROM t_stock_document JOIN t_document ON t_document.document_id = t_stock_document.document_id WHERE t_stock_document.stock_id = t_stock.stock_id) AS document_type_list,
            t_stock.facility_cd,
            m_facility.facility_category_type,
            m_facility.facility_name,
            m_facility.facility_short_name,
            m_department.department_cd,
            m_department.department_name,
            t_stock.person_cd,
            m_person.person_name,
            m_content.content_cd,
            m_content.content_name,
            t_stock.status_type,
            t_stock.stock_type,
            t_stock.action_id,
            t_stock.stock_date 
        FROM
            t_stock
            JOIN m_facility ON m_facility.facility_cd = t_stock.facility_cd
            LEFT JOIN m_facility_person ON m_facility_person.facility_cd = t_stock.facility_cd 
            AND m_facility_person.person_cd = t_stock.person_cd
            LEFT JOIN m_department ON m_department.department_cd = m_facility_person.department_cd
            LEFT JOIN m_person ON m_person.person_cd = t_stock.person_cd
            LEFT JOIN m_content ON m_content.content_cd = t_stock.content_cd
            LEFT JOIN t_stock_product ON t_stock_product.stock_id = t_stock.stock_id
            LEFT JOIN m_product ON m_product.product_cd = t_stock_product.product_cd
        WHERE t_stock.created_by = :user_cd";
        $condition_values['user_cd'] = $conditions->user_cd;
        if ($conditions->content_cd != "") {
            $sql .= " AND t_stock.content_cd = :content_cd";
            $condition_values['content_cd'] = $conditions->content_cd;
        }
        if (!empty($conditions->product_cd)) {
            $sql .= " AND m_product.product_cd IN " . $this->_buildCommaString($conditions->product_cd, true);
        }
        if ($conditions->status_type !== "" && $conditions->status_type !== null) {
            $sql .= " AND t_stock.status_type = :status_type";
            $condition_values['status_type'] = $conditions->status_type == 1 ? STOCK_STATUS_UNPLANNED : STOCK_STATUS_PLANNED;
        }
        if (!empty($conditions->facility_cd)) {
            $sql .= " AND m_facility.facility_cd IN " . $this->_buildCommaString($conditions->facility_cd, true);
        }
        if ($conditions->facility_name != "") {
            $sql .= " AND m_facility.facility_name LIKE :facility_name";
            $condition_values['facility_name'] = "%".trim($this->convert_kana($conditions->facility_name))."%";
        }
        if ($conditions->facility_category_type != "") {
            $sql .= " AND m_facility.facility_category_type = :facility_category_type";
            $condition_values['facility_category_type'] = $conditions->facility_category_type;
        }
        if ($conditions->person_name != "") {
            $sql .= " AND m_person.person_name LIKE :person_name";
            $condition_values['person_name'] = "%".trim($this->convert_kana($conditions->person_name))."%";
        }
        $sql .= " GROUP BY t_stock.stock_id ORDER BY t_stock.stock_id DESC";
        if (!empty($conditions->facility_cd)) {
            return $this->_find($sql, $condition_values);
        }

        return $this->_paginate($sql, $condition_values, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function edit($data)
    {
        if ($data->status_type === null || $data->status_type === "") {
            $sql_update_content = "UPDATE t_stock SET content_cd = :content_cd WHERE stock_id IN " . $this->_buildCommaString($data->stock_id, true);
            $sql_stock_product = "DELETE FROM t_stock_product WHERE stock_id IN " . $this->_buildCommaString($data->stock_id, true);
            $sql_stock_document = "DELETE FROM t_stock_document WHERE stock_id IN " . $this->_buildCommaString($data->stock_id, true);

            $sql_tock_product = "INSERT INTO 
                t_stock_product (
                    stock_id, 
                    product_cd
                ) VALUES :values
            ";
            $sql_tock_document = "INSERT INTO 
                t_stock_document (
                    stock_id, 
                    document_id
                ) VALUES :values
            ";
            $result_1 = $this->_update($sql_update_content, ['content_cd' => $data->content_cd ?? null]);
            $result_2 = $this->_destroy($sql_stock_product, []);
            $result_3 = $this->_destroy($sql_stock_document, []);
            if (!empty($data->products)) {
                $result_4 = $this->_bulkCreate($sql_tock_product, $data->products);
            } else {
                $result_4 = true;
            }
            if (!empty($data->documents)) {
                $result_5 = $this->_bulkCreate($sql_tock_document, $data->documents);
            } else {
                $result_5 = true;
            }

            return $result_1 && $result_2 && $result_3 && $result_4 && $result_5;
        } else {
            $data->status_type = $data->status_type == 1 ? STOCK_STATUS_UNPLANNED : STOCK_STATUS_PLANNED;
            $sql_update_content = "UPDATE t_stock SET status_type = :status_type WHERE stock_id IN " . $this->_buildCommaString($data->stock_id, true);
            return $this->_update($sql_update_content, ['status_type' => $data->status_type]);
        }
    }

    public function create($data)
    {
        $sql = "INSERT INTO 
            t_stock (
                facility_cd,
                person_cd,
                content_cd,
                status_type,
                stock_type,
                action_id,
                stock_date
            ) VALUES (
                :facility_cd,
                :person_cd,
                :content_cd,
                :status_type,
                :stock_type,
                :action_id,
                :stock_date
            );";
        return $this->_create($sql, $data);
    }

    public function createStockProduct($data)
    {
        $sql = "INSERT INTO 
            t_stock_product (
                stock_id,
                product_cd
            ) VALUES :values";
        return $this->_bulkCreate($sql, $data);
    }

    public function _lastInsertedStock()
    {
        return $this->_lastInserted('t_stock', 'stock_id');
    }

    public function delete($conditions)
    {
        $sql_stock_product = "DELETE FROM t_stock_product WHERE stock_id IN " . $this->_buildCommaString($conditions->stock_id, true);
        $sql_stock_document = "DELETE FROM t_stock_document WHERE stock_id IN " . $this->_buildCommaString($conditions->stock_id, true);
        $sql_stock = "DELETE FROM t_stock WHERE stock_id IN " . $this->_buildCommaString($conditions->stock_id, true);
        $result_1 = $this->_destroy($sql_stock_product, []);
        $result_2 = $this->_destroy($sql_stock_document, []);
        $result_3 = $this->_destroy($sql_stock, []);
        return $result_1 && $result_2 && $result_3;
    }

    public function contents()
    {
        $sql = "SELECT content_cd, content_name FROM m_content";
        return $this->_all($sql);
    }

    public function facilityCategory()
    {
        $sql = "SELECT facility_category_type, facility_category_name FROM m_facility_category";
        return $this->_all($sql);
    }
}
