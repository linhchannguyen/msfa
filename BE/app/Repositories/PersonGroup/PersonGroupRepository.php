<?php

namespace App\Repositories\PersonGroup;

use App\Repositories\BaseRepository;
use App\Repositories\PersonGroup\PersonGroupRepositoryInterface;
use App\Traits\DateTimeTrait;

class PersonGroupRepository extends BaseRepository implements PersonGroupRepositoryInterface
{
    use DateTimeTrait;
    private $date;
    protected $useAutoMeta = true;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function allData($request)
    {
        $where = "";
        $query = [];
        $where .= " AND user_cd = :user_cd ";
        $query['user_cd'] = $request->user_login;
        if ($request->isCopy) {
            if ($request->user_cd) {
                $query['user_cd'] = $request->user_cd;
            }
        }
        $sql = "SELECT select_person_group_id, select_person_group_name, user_cd, sort_order
                FROM t_select_person_group 
                WHERE 1 = 1 " . $where . " ORDER BY sort_order";
        return $this->_find($sql, $query);
    }

    public function listFacilityType($select_person_group_id)
    {
        $sql = "SELECT T2.facility_category_type,
                        T3.facility_category_name, 
                        T1.select_person_group_id,
                        T4.content_cd,
                        T5.content_name,
                        (SELECT GROUP_CONCAT(A2.product_name separator ', ')
                        FROM t_select_person_group_product A1 JOIN m_product A2 ON A1.product_cd=A2.product_cd
                        WHERE A1.facility_category_type = T2.facility_category_type  AND A1.select_person_group_id = T1.select_person_group_id) AS product_name,
                        (SELECT GROUP_CONCAT(A2.product_cd separator ', ')
                        FROM t_select_person_group_product A1 JOIN m_product A2 ON A1.product_cd=A2.product_cd
                        WHERE A1.facility_category_type = T2.facility_category_type  AND A1.select_person_group_id = T1.select_person_group_id) AS product_cd,
                        (SELECT GROUP_CONCAT(A2.document_name separator ', ')
                        FROM t_select_person_group_document A1 JOIN t_document A2 ON A1.document_id=A2.document_id
                        WHERE A1.facility_category_type = T2.facility_category_type  AND A1.select_person_group_id = T1.select_person_group_id) AS document_name,
                        (SELECT GROUP_CONCAT(A2.document_id separator ',')
                        FROM t_select_person_group_document A1 JOIN t_document A2 ON A1.document_id=A2.document_id
                        WHERE A1.facility_category_type = T2.facility_category_type  AND A1.select_person_group_id = T1.select_person_group_id) AS document_id,
                        (SELECT GROUP_CONCAT(A2.file_type separator ',') AS file_type
                        FROM t_select_person_group_document A1 JOIN t_document A2 ON A1.document_id=A2.document_id
                        WHERE A1.facility_category_type = T2.facility_category_type  AND A1.select_person_group_id = T1.select_person_group_id) AS file_type,
                        (SELECT GROUP_CONCAT(A2.document_type separator ',') AS document_type
                        FROM t_select_person_group_document A1 JOIN t_document A2 ON A1.document_id=A2.document_id
                        WHERE A1.facility_category_type = T2.facility_category_type  AND A1.select_person_group_id = T1.select_person_group_id) AS document_type
                FROM  t_select_person_group_detail T1 
                JOIN m_facility T2 on T1.facility_cd = T2.facility_cd
                JOIN m_facility_category T3 on T2.facility_category_type = T3.facility_category_type
                LEFT JOIN t_select_person_group_content T4 on T2.facility_category_type = T4.facility_category_type AND T4.select_person_group_id = T1.select_person_group_id
                LEFT JOIN m_content T5 on T4.content_cd = T5.content_cd
                where T1.select_person_group_id = :select_person_group_id
                GROUP BY T2.facility_category_type
                ORDER BY T2.facility_category_type;";
        return $this->_find($sql, ['select_person_group_id' => $select_person_group_id]);
    }

    public function allPerson($select_person_group_id, $facility_category_type)
    {
        $sql = "SELECT T2.facility_cd, T2.facility_short_name, T2.facility_name, T4.person_cd, T4.person_name, T7.department_cd, T7.department_name,T8.position_cd,T8.position_name
                FROM  t_select_person_group_detail T1 
                JOIN m_facility T2 ON T1.facility_cd = T2.facility_cd
                JOIN m_facility_category T9 ON T2.facility_category_type = T9.facility_category_type
                join m_facility_person T3 on T1.facility_cd = T3.facility_cd and T3.person_cd = T1.person_cd
                JOIN m_person T4 on T3.person_cd = T4.person_cd
                JOIN m_department T7 on T3.department_cd = T7.department_cd
                LEFT JOIN m_position T8 on T3.hospital_position_cd = T8.position_cd
                WHERE T1.select_person_group_id = :select_person_group_id
                    AND T2.facility_category_type = :facility_category_type
                ORDER BY T2.facility_category_type, T2.facility_short_name_kana,T7.department_cd,T4.person_name_kana";
        return $this->_find($sql, ['select_person_group_id' => $select_person_group_id, 'facility_category_type' => $facility_category_type]);
    }

    public function deletePersonGroup($select_person_group_id)
    {
        $sql = "DELETE FROM t_select_person_group WHERE select_person_group_id = :select_person_group_id ";
        return $this->_destroy($sql, ['select_person_group_id' => $select_person_group_id]);
    }

    public function deletePersonGroupContent($select_person_group_id)
    {
        $sql = "DELETE FROM t_select_person_group_content WHERE select_person_group_id = :select_person_group_id ";
        return $this->_destroy($sql, ['select_person_group_id' => $select_person_group_id]);
    }

    public function deletePersonGroupDocument($select_person_group_id)
    {
        $sql = "DELETE FROM t_select_person_group_document WHERE select_person_group_id = :select_person_group_id ";
        return $this->_destroy($sql, ['select_person_group_id' => $select_person_group_id]);
    }

    public function deletePersonGroupProduct($select_person_group_id)
    {
        $sql = "DELETE FROM t_select_person_group_product WHERE select_person_group_id = :select_person_group_id ";
        return $this->_destroy($sql, ['select_person_group_id' => $select_person_group_id]);
    }

    public function updatePersonGroup($data)
    {
        $sql_update = "UPDATE t_select_person_group SET 
                            select_person_group_name = :select_person_group_name, 
                            sort_order = :sort_order
                        WHERE
                        select_person_group_id = :select_person_group_id";
        $parram = array(
            "select_person_group_id" => $data->select_person_group_id,
            "sort_order" => $data->sort_order,
            "select_person_group_name" => $data->select_person_group_name
        );
        $this->_update($sql_update, $parram);
        return $data->select_person_group_id;
    }

    public function insertPersonGroup($data)
    {
        $sql = "INSERT INTO t_select_person_group (
                            user_cd,
                            select_person_group_name,
                            sort_order)
                VALUES (
                    :user_cd,
                    :select_person_group_name,
                    :sort_order);";
        $parram = [
            'user_cd' => $data->user_cd,
            'select_person_group_name' => $data->select_person_group_name,
            'sort_order' => $data->sort_order
        ];
        $create = $this->_create($sql, $parram);
        $lastInserted = $this->_lastInserted("t_select_person_group", "select_person_group_id");
        return $lastInserted->select_person_group_id;
    }

    public function updateGroupContent($user_cd, $person_group_id, $content_cd, $facility_category_type)
    {
        $sql_update = "INSERT INTO t_select_person_group_content (
                            select_person_group_id,
                            facility_category_type,
                            content_cd) 
                        VALUES(
                            :select_person_group_id,
                            :facility_category_type,
                            :content_cd
                        );";
        $parram = array(
            'select_person_group_id' => $person_group_id,
            'facility_category_type' => $facility_category_type,
            'content_cd' => $content_cd
        );
        return $this->_create($sql_update, $parram);
    }

    public function updateGroupProduct($user_cd, $person_group_id, $product_cd, $facility_category_type)
    {
        $sql_update = "INSERT INTO t_select_person_group_product (
                            select_person_group_id,
                            facility_category_type,
                            product_cd) 
                        VALUES(
                            :select_person_group_id,
                            :facility_category_type,
                            :product_cd
                        );";
        $parram = array(
            'select_person_group_id' => $person_group_id,
            'facility_category_type' => $facility_category_type,
            'product_cd' => $product_cd
        );
        return $this->_create($sql_update, $parram);
    }

    public function updateGroupDocument($user_cd, $person_group_id, $document, $facility_category_type)
    {
        $sql_update = "INSERT INTO t_select_person_group_document (
                            select_person_group_id,
                            facility_category_type,
                            document_id) 
                        VALUES(
                            :select_person_group_id,
                            :facility_category_type,
                            :document_id
                        );";
        $parram = array(
            'select_person_group_id' => $person_group_id,
            'facility_category_type' => $facility_category_type,
            'document_id' => $document
        );
        return $this->_create($sql_update, $parram);
    }

    public function deleteGroupDetail($person_group_id)
    {
        $sql = "DELETE FROM t_select_person_group_detail WHERE select_person_group_id = :select_person_group_id ";
        return $this->_destroy($sql, ['select_person_group_id' => $person_group_id]);
    }

    public function insertGroupDetail($user_cd, $select_person_group_id, $facility_cd, $person_cd)
    {
        $sql = "INSERT INTO t_select_person_group_detail (
                            select_person_group_id,
                            facility_cd,
                            person_cd)
                VALUES (
                    :select_person_group_id,
                    :facility_cd,
                    :person_cd);";
        $parram = [
            'select_person_group_id' => $select_person_group_id,
            'facility_cd' => $facility_cd,
            'person_cd' => $person_cd
        ];
        return $this->_create($sql, $parram);
    }
}
