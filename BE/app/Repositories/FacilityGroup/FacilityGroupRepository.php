<?php

namespace App\Repositories\FacilityGroup;

use App\Repositories\BaseRepository;
use App\Repositories\FacilityGroup\FacilityGroupRepositoryInterface;
use App\Traits\DateTimeTrait;

class FacilityGroupRepository extends BaseRepository implements FacilityGroupRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = true;
    private $date;
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
        $sql = "SELECT select_facility_group_id, select_facility_group_name, user_cd, sort_order
                FROM t_select_facility_group WHERE 1 = 1 " . $where . " ORDER BY sort_order";
        return $this->_find($sql, $query);
    }

    public function allFacility($select_facility_group_id)
    {
        $sql = "SELECT T3.facility_cd, T3.facility_short_name
                FROM t_select_facility_group T1 
                    JOIN t_select_facility_group_detail T2 ON T1.select_facility_group_id = T2.select_facility_group_id 
                    JOIN m_facility T3 ON T3.facility_cd = T2.facility_cd 
                WHERE T1.select_facility_group_id = :select_facility_group_id";
        return $this->_find($sql, ['select_facility_group_id' => $select_facility_group_id]);
    }

    public function deleteFacilityGroup($select_facility_group_id)
    {
        $sql = "DELETE FROM t_select_facility_group WHERE select_facility_group_id = :select_facility_group_id ";
        return $this->_destroy($sql, ['select_facility_group_id' => $select_facility_group_id]);
    }

    public function updateFacilityGroup($data)
    {
        $sql_update = "UPDATE t_select_facility_group
                        SET 
                            select_facility_group_name = :select_facility_group_name, 
                            sort_order = :sort_order
                        WHERE
                            select_facility_group_id = :select_facility_group_id";
        $parram = array(
            'select_facility_group_name' => $data->select_facility_group_name,
            'select_facility_group_id' => $data->select_facility_group_id,
            'sort_order' => $data->sort_order
        );
        $this->_update($sql_update, $parram);
        return $data->select_facility_group_id;
    }

    public function insertFacilityGroup($data)
    {
        $sql_update = "INSERT INTO t_select_facility_group (
                                    user_cd,
                                    select_facility_group_name,
                                    sort_order)
                        VALUES (
                            :user_cd,
                            :select_facility_group_name,
                            :sort_order
                        );";
        $parram = array(
            'user_cd' => $data->user_cd,
            'select_facility_group_name' => $data->select_facility_group_name,
            'sort_order' => $data->sort_order
        );
        $create = $this->_create($sql_update, $parram);

        $lastInserted = $this->_lastInserted("t_select_facility_group", "select_facility_group_id");
        return $lastInserted->select_facility_group_id;
    }

    public function deleteFacility($select_facility_group_id)
    {
        $sql = "DELETE FROM t_select_facility_group_detail WHERE select_facility_group_id = :select_facility_group_id ";
        return $this->_destroy($sql, ['select_facility_group_id' => $select_facility_group_id]);
    }

    public function updateFacility($select_facility_group_id, $facility_cd, $user_cd)
    {
        $sql_update = "INSERT INTO t_select_facility_group_detail (
                                    select_facility_group_id,
                                    facility_cd)
                        VALUES (
                            :select_facility_group_id,
                            :facility_cd
                        );";
        $parram = array(
            'select_facility_group_id' => $select_facility_group_id,
            'facility_cd' => $facility_cd
        );
        return $this->_create($sql_update, $parram);
    }
}
