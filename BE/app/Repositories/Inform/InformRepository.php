<?php

namespace App\Repositories\Inform;

use App\Repositories\BaseRepository;
use App\Repositories\Inform\InformRepositoryInterface;
use App\Traits\StringConvertTrait;
use App\Traits\DateTimeTrait;

class InformRepository extends BaseRepository implements InformRepositoryInterface
{
    use StringConvertTrait, DateTimeTrait;
    protected $useAutoMeta = true;
    private $date;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function search($data, $limit, $offset)
    {
        $condition_values = [];
        $sql = "SELECT
            i.inform_id,
            i.inform_category_cd,
            ic.inform_category_name,
            i.inform_datetime,
            i.inform_user_cd,
            i.inform_contents,
            i.archive_flag,
            i.informed_flag,
            i.read_flag,
            ip.parameter_key,
            ip.parameter_value
        FROM
            t_inform i
            JOIN m_inform_category ic ON i.inform_category_cd = ic.inform_category_cd 
            JOIN t_inform_parameter ip ON i.inform_id = ip.inform_id
        WHERE
            i.inform_user_cd = :user_cd ";
        $condition_values['user_cd'] = $data->user_cd;
        if (!empty($data->inform_category_cd)) {
            $sql .= "AND i.inform_category_cd IN (";
            foreach ($data->inform_category_cd as $index => $category) {
                if ($index === 0) {
                    $sql .= ":inform_category_cd$index";
                } else {
                    $sql .= ",:inform_category_cd$index";
                }
                $condition_values["inform_category_cd$index"] = $category;
            }
            $sql .= ") ";
        }
        if ($this->convDATE($data->inform_datetime_from) > $this->convDATE(0)) {
            $sql .= "AND CAST(i.inform_datetime as DATE) >= :inform_datetime_from ";
            $condition_values['inform_datetime_from'] = $data->inform_datetime_from;
        }
        if ($this->convDATE($data->inform_datetime_to) > $this->convDATE(0)) {
            $sql .= "AND CAST(i.inform_datetime as DATE) <= :inform_datetime_to ";
            $condition_values['inform_datetime_to'] = $data->inform_datetime_to;
        }
        $sql .= " AND i.archive_flag = :archive_flag ";
        $condition_values['archive_flag'] = $data->archive_flag;
        if (trim($data->inform_contents) !== "") {
            $sql .= "AND i.inform_contents LIKE :inform_contents ";
            $data->inform_contents = trim($this->convert_kana($data->inform_contents));
            $condition_values['inform_contents'] = "%$data->inform_contents%";
        }
        $sql .= "ORDER BY inform_datetime DESC";
        return $this->_paginate($sql, $condition_values, [
            "limit" => $limit,
            "offset" => $offset,
            "key" => "*",
            "key_type" => "normal"
        ]);
    }

    public function archived($data)
    {
        $condition_values = [];
        $sql = "UPDATE t_inform 
        SET archive_flag = :archive_flag
        WHERE
            archive_flag = 0 
            AND inform_user_cd = :inform_user_cd ";
        $condition_values['archive_flag'] = 1;
        $condition_values['inform_user_cd'] = $data->user_cd;
        $sql .= "AND inform_id IN (";
        foreach ($data->inform_id as $index => $inform_id) {
            if ($index === 0) {
                $sql .= ":inform_id$index";
            } else {
                $sql .= ",:inform_id$index";
            }
            $condition_values["inform_id$index"] = $inform_id;
        }
        $sql .= ")";

        return $this->_update($sql, $condition_values);
    }

    public function unarchived($data)
    {
        $condition_values = [];
        $sql = "UPDATE t_inform 
        SET archive_flag = :archive_flag 
        WHERE
            archive_flag = 1 
            AND inform_user_cd = :inform_user_cd ";
        $condition_values['archive_flag'] = 0;
        $condition_values['inform_user_cd'] = $data->user_cd;
        $sql .= "AND inform_id IN (";
        foreach ($data->inform_id as $index => $inform_id) {
            if ($index === 0) {
                $sql .= ":inform_id$index";
            } else {
                $sql .= ",:inform_id$index";
            }
            $condition_values["inform_id$index"] = $inform_id;
        }
        $sql .= ")";

        return $this->_update($sql, $condition_values);
    }

    public function archiveAll($user_cd)
    {
        $sql = "UPDATE t_inform 
        SET archive_flag = :archive_flag
        WHERE
            archive_flag = 0 
            AND inform_user_cd = :user_cd";

        return $this->_update($sql, ['user_cd' => $user_cd, 'archive_flag' => 1]);
    }

    public function readInform($id)
    {
        $sql = "UPDATE t_inform 
        SET read_flag = :read_flag
        WHERE inform_id = :id";

        return $this->_update($sql, ['id' => $id, 'read_flag' => 1]);
    }

    public function installed($user_cd, $inform_category_cd = null)
    {
        $sql = "SELECT
            c.inform_category_cd,
            c.inform_category_name,
            ( CASE WHEN ue.user_cd IS NULL THEN 1 ELSE 0 END ) AS checked 
        FROM
            m_inform_category c
            LEFT JOIN t_user_exclusion_inform_category ue ON c.inform_category_cd = ue.inform_category_cd 
            AND ue.user_cd = :user_cd 
        WHERE
            c.inform_stop_flag = 0";
        if (!empty($inform_category_cd)) {
            $sql .= " AND c.inform_category_cd = :inform_category_cd";
            $condition['inform_category_cd'] = $inform_category_cd;
        }
        $sql .= " ORDER BY c.sort_order ASC";
        $condition['user_cd'] = $user_cd;
        return $this->_find($sql, $condition);
    }

    public function register($data)
    {
        $insert = "INSERT INTO 
            t_user_exclusion_inform_category (
                inform_category_cd, 
                user_cd
            ) VALUES :values
        ;";
        return $this->_bulkCreate($insert, $data);
    }

    public function delete($user_cd)
    {
        $delete = "DELETE 
            FROM
                t_user_exclusion_inform_category 
            WHERE
                user_cd = :user_cd";
        return $this->_destroy($delete, ['user_cd' => $user_cd]);
    }

    static public function convDATE($dateStr, $format = 'Y-m-d')
    {
        if (($dateStr == '0000/00/00') || empty($dateStr) || ($dateStr == '0000-00-00')) {
            $date = '0000-00-00';
        } else {
            $date = date($format, strtotime($dateStr));
        }
        return $date;
    }

    public function registerInform($user_cd = null, $user_approval, $content, $category = null)
    {
        $sql = "INSERT INTO t_inform (inform_category_cd,
                                inform_datetime
                                ,inform_user_cd
                                ,inform_contents)
                VALUES(:inform_category_cd
                        ,:inform_datetime
                        ,:inform_user_cd
                        ,:inform_contents);";
        $parram = [
            'inform_category_cd' => empty($category) ? 200 : $category,
            'inform_datetime' => $this->date,
            'inform_user_cd' => $user_approval,
            'inform_contents' => $content
        ];

        $this->_create($sql, $parram);
        $lastInserted = $this->_lastInserted("t_inform", "inform_id");
        return $lastInserted->inform_id;
    }

    public function registerInformParameter($user_cd = null, $inform_id, $parameter_key, $parameter_value)
    {
        $sql = "INSERT INTO t_inform_parameter (
                                inform_id
                                ,parameter_key
                                ,parameter_value)
                VALUES(:inform_id
                        ,:parameter_key
                        ,:parameter_value);";
        $parram = [
            'inform_id' => $inform_id,
            'parameter_key' => $parameter_key,
            'parameter_value' => $parameter_value
        ];

        return $this->_create($sql, $parram);
    }
}
