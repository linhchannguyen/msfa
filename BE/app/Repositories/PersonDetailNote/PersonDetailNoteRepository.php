<?php

namespace App\Repositories\PersonDetailNote;

use App\Repositories\BaseRepository;
use App\Traits\DateTimeTrait;
use App\Traits\StringConvertTrait;

class PersonDetailNoteRepository extends BaseRepository implements PersonDetailNoteRepositoryInterface
{
    use StringConvertTrait;
    protected $useAutoMeta = true;
    use DateTimeTrait;
    public function allConsiderationType()
    {
        $sql = "SELECT consideration_type, consideration_type_name, use_facility_flag, use_person_flag FROM m_consideration_type";
        return $this->_all($sql);
    }

    public function searchConsideration($conditions)
    {
        $condition_query = [];
        $sql = "SELECT
            t_person_consideration.person_consideration_id,
            t_person_consideration.person_cd,
            t_person_consideration.consideration_type,
            m_consideration_type.consideration_type_name,
            t_person_consideration.consideration_contents,
            t_person_consideration.last_update_datetime,
            t_person_consideration.create_user_cd,
            t_person_consideration.create_user_name,
            t_person_consideration.create_org_cd,
            t_person_consideration.create_org_label,
            t_person_consideration.updated_by,
            (SELECT CONCAT('[', GROUP_CONCAT(JSON_OBJECT(
                'user_cd', t_person_consideration_confirm.user_cd,
                'user_name', m_user.user_name,
                'confirmed_flag', t_person_consideration_confirm.confirmed_flag,
                'confirmed_datetime', t_person_consideration_confirm.confirmed_datetime
                )),']') FROM t_person_consideration_confirm
                JOIN m_user ON m_user.user_cd = t_person_consideration_confirm.user_cd
                WHERE t_person_consideration_confirm.person_consideration_id = t_person_consideration.person_consideration_id) AS user_confirm_list
        FROM t_person_consideration
        JOIN m_consideration_type ON m_consideration_type.consideration_type = t_person_consideration.consideration_type
        WHERE t_person_consideration.person_cd = :person_cd";
        $condition_query['person_cd'] = $conditions->person_cd;
        if($conditions->consideration_contents != ""){
            $sql .= " AND t_person_consideration.consideration_contents LIKE :consideration_contents";
            $condition_query['consideration_contents'] = "%".trim($this->convert_kana($conditions->consideration_contents))."%";
        }
        if($conditions->last_update_datetime_from != ""){
            $sql .= " AND CAST(t_person_consideration.last_update_datetime AS DATE) >= :last_update_datetime_from";
            $condition_query['last_update_datetime_from'] = $conditions->last_update_datetime_from;
        }
        if($conditions->last_update_datetime_to != ""){
            $sql .= " AND CAST(t_person_consideration.last_update_datetime AS DATE) <= :last_update_datetime_to";
            $condition_query['last_update_datetime_to'] = $conditions->last_update_datetime_to;
        }
        if(!empty($conditions->consideration_types)){
            $sql .= " AND t_person_consideration.consideration_type IN " . $this->_buildCommaString($conditions->consideration_types, true);
        }
        $sql .= " ORDER BY last_update_datetime DESC";
        return $this->_find($sql, $condition_query);
    }

    public function getConsiderationById($id)
    {
        $sql = "SELECT updated_by, create_user_cd FROM t_person_consideration WHERE person_consideration_id = :person_consideration_id";
        return $this->_find($sql, ['person_consideration_id' => $id]);
    }

    public function getConsiderationConfirmById($id)
    {
        $sql = "SELECT person_consideration_id, user_cd FROM t_person_consideration_confirm WHERE person_consideration_id = :person_consideration_id";
        return $this->_find($sql, ['person_consideration_id' => $id]);
    }

    public function considerationConfirm($conditions)
    {
        $sql = "UPDATE t_person_consideration_confirm SET confirmed_flag = :confirmed_flag, confirmed_datetime = :confirmed_datetime WHERE person_consideration_id = :person_consideration_id AND user_cd = :user_cd";
        return $this->_update($sql, [
            'confirmed_flag' => $conditions->confirmed_flag ?? 0,
            'person_consideration_id' => $conditions->person_consideration_id ?? 0,
            'user_cd' => $conditions->user_cd,
            'confirmed_datetime' => $conditions->confirmed_flag ? $this->currentJapanDateTime() : null,
        ]);
    }

    public function getPersonInChargeList($person_cd)
    {
        $sql = "SELECT
            m_facility_user.user_cd
        FROM m_facility_person
        JOIN m_facility_user ON m_facility_user.facility_cd = m_facility_person.facility_cd
        WHERE person_cd = :person_cd
        GROUP BY m_facility_user.user_cd";
        return $this->_find($sql, ['person_cd' => $person_cd]);
    }

    public function createPersonConsideration($datas)
    {
        $sql = "INSERT INTO t_person_consideration (person_cd, consideration_type, consideration_contents, last_update_datetime, create_user_cd, create_user_name, create_org_cd, create_org_label) VALUES :values";
        return $this->_bulkCreate($sql, $datas);
    }

    public function updatePersonConsideration($datas)
    {
        $sql = "UPDATE t_person_consideration SET 
            consideration_type = :consideration_type, 
            consideration_contents = :consideration_contents, 
            last_update_datetime = :last_update_datetime
            WHERE person_consideration_id = :person_consideration_id";
        return $this->_update($sql, $datas);
    }

    public function createPersonConsiderationConfirm($datas)
    {
        $sql = "INSERT INTO t_person_consideration_confirm (person_consideration_id, user_cd) VALUES :values";
        return $this->_bulkCreate($sql, $datas);
    }

    public function lastInsertedPersonConsideration()
    {
        return $this->_lastInserted('t_person_consideration', 'person_consideration_id');
    }

    public function deleteConsideration($id)
    {
        $deleteConsiderationConfirm = "DELETE FROM t_person_consideration_confirm WHERE person_consideration_id = :person_consideration_id";
        $deleteConsideration = "DELETE FROM t_person_consideration WHERE person_consideration_id = :person_consideration_id";
        $result_1 = $this->_destroy($deleteConsiderationConfirm, ['person_consideration_id' => $id]);
        $result_2 = $this->_destroy($deleteConsideration, ['person_consideration_id' => $id]);
        return $result_1 && $result_2;
    }

    public function deleteConsiderationConfirm($conditions)
    {
        $deleteConsiderationConfirm = "DELETE FROM t_person_consideration_confirm WHERE person_consideration_id = :person_consideration_id AND user_cd IN ". $this->_buildCommaString($conditions['user_cd'], true);
        return $this->_destroy($deleteConsiderationConfirm, ['person_consideration_id' => $conditions['person_consideration_id']]);
    }
}