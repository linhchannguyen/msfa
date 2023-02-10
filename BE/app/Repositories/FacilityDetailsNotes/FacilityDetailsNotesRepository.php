<?php

namespace App\Repositories\FacilityDetailsNotes;

use App\Repositories\BaseRepository;
use App\Repositories\FacilityDetailsNotes\FacilityDetailsNotesRepositoryInterface;
use App\Traits\StringConvertTrait;

class FacilityDetailsNotesRepository extends BaseRepository implements FacilityDetailsNotesRepositoryInterface
{
    protected $useAutoMeta = true;

    use StringConvertTrait;

    public function getScreenData()
    {
        $sql = "SELECT consideration_type,consideration_type_name FROM m_consideration_type ORDER BY consideration_type ASC;";
        return $this->_find($sql,[]);
    }

    public function getFacilityDetailsNotes($facility_cd,$content,$start_date,$end_date,$consideration_type)
    {
        $sql = "
            SELECT 
                t_facility_consideration.consideration_type,
                m_consideration_type.consideration_type_name,
                t_facility_consideration.create_org_label,
                t_facility_consideration.create_user_name,
                DATE_FORMAT(t_facility_consideration.last_update_datetime,'%Y/%m/%d') as last_update_datetime,
                t_facility_consideration.consideration_contents,
                t_facility_consideration.facility_consideration_id,
                t_facility_consideration.create_user_cd,
                t_facility_consideration.updated_by,
                m_facility.facility_cd,
                m_facility.facility_name,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                            'user_cd', t_facility_consideration_confirm.user_cd,
                                            'confirmed_flag', t_facility_consideration_confirm.confirmed_flag,
                                            'org_cd' , m_user_org.org_cd,
                                            'org_label' , m_org.org_label,
                                            'user_name' , m_user.user_name
                                        ) 
                                    ), 
                                ']' 
                )FROM t_facility_consideration_confirm
                LEFT JOIN m_user
                    ON m_user.user_cd = t_facility_consideration_confirm.user_cd
                LEFT JOIN m_user_org
                    ON (m_user.user_cd = m_user_org.user_cd AND m_user_org.main_flag = 1)
                LEFT JOIN m_org
                    ON m_user_org.org_cd = m_org.org_cd
                WHERE t_facility_consideration_confirm.facility_consideration_id = t_facility_consideration.facility_consideration_id
                ) as announcement,
                (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 'user_cd', m_facility_user.user_cd) ), ']' ) FROM m_facility_user WHERE m_facility_user.facility_cd = t_facility_consideration.facility_cd )as m_facility_user
            FROM t_facility_consideration
            LEFT JOIN m_consideration_type
                ON t_facility_consideration.consideration_type = m_consideration_type.consideration_type
            LEFT JOIN t_facility_consideration_confirm
                ON t_facility_consideration_confirm.facility_consideration_id = t_facility_consideration.facility_consideration_id
            INNER JOIN m_facility
                ON m_facility.facility_cd = t_facility_consideration.facility_cd
            WHERE t_facility_consideration.facility_cd = :facility_cd";

        if(!empty($content)){
            $sql .= " AND t_facility_consideration.consideration_contents LIKE :content ";
            $query['content'] = "%".trim($this->convert_kana($content))."%";
        }

        if(!empty($start_date)){
            $sql .= " AND t_facility_consideration.last_update_datetime >= :start_date";
            $query['start_date'] = $start_date;
        }

        if(!empty($end_date)){
            $sql .= " AND t_facility_consideration.last_update_datetime <=:end_date";
            $query['end_date'] = $end_date;
        }

        if(!empty($consideration_type)){
            $consideration_type = explode(',',$consideration_type);
            $sql .= " AND t_facility_consideration.consideration_type IN ". $this->_buildCommaString($consideration_type, true ) . " ";
        }

        $sql .= " GROUP BY t_facility_consideration.facility_consideration_id
        ORDER BY t_facility_consideration.last_update_datetime DESC;";

        $query['facility_cd'] = $facility_cd;

        return $this->_find($sql,$query);
    }

    public function saveConsiderationConfirm($facility_consideration_id, $confirmed_flag,$user_cd,$confirmed_datetime)
    {
        $sql = "
            UPDATE t_facility_consideration_confirm
                SET confirmed_flag = :confirmed_flag,
                    confirmed_datetime = :confirmed_datetime
            WHERE facility_consideration_id = :facility_consideration_id
            AND user_cd = :user_cd ;";

        return $this->_update($sql,['facility_consideration_id' => $facility_consideration_id,'confirmed_flag' => $confirmed_flag , 'user_cd' => $user_cd , 'confirmed_datetime' => $confirmed_datetime]);
    }

    public function deleteConsideration($facility_consideration_id)
    {
        $sql = "DELETE FROM t_facility_consideration WHERE facility_consideration_id = :facility_consideration_id;";
        $consideration = $this->_destroy($sql,['facility_consideration_id' => $facility_consideration_id]);

        $sql = "DELETE FROM t_facility_consideration_confirm WHERE facility_consideration_id = :facility_consideration_id;";
        $consideration_confirm = $this->_destroy($sql,['facility_consideration_id' => $facility_consideration_id]);

        return $consideration && $consideration_confirm;
    }

    public function saveFacilityConsiderationNewMode($facility_cd,$consideration_type,$consideration_contents,$user_cd_login,$user_name_login,$org_cd_login,$org_label_login,$data_system)
    {
        $sql = "INSERT INTO t_facility_consideration (facility_cd,consideration_type,consideration_contents,last_update_datetime,create_user_cd,create_user_name,create_org_cd,create_org_label)
         VALUES (:facility_cd,:consideration_type,:consideration_contents,:last_update_datetime,:create_user_cd,:create_user_name,:create_org_cd,:create_org_label);";

        $this->_create($sql,[
            'facility_cd' => $facility_cd,
            'consideration_type' => $consideration_type,
            'consideration_contents' => $consideration_contents,
            'last_update_datetime' => $data_system,
            'create_user_cd' => $user_cd_login,
            'create_user_name' => $user_name_login,
            'create_org_cd' => $org_cd_login,
            'create_org_label' => $org_label_login
        ]);

        $t_facility_consideration = $this->_lastInserted('t_facility_consideration', 'facility_consideration_id');

        return $t_facility_consideration->facility_consideration_id;
    }

    public function checkFacilityConsiderationConfirm($facility_consideration_id, $user_cd)
    {
        $sql = "SELECT facility_consideration_id FROM t_facility_consideration_confirm WHERE facility_consideration_id = :facility_consideration_id AND user_cd = :user_cd;";
        return $this->_first($sql,['facility_consideration_id' => $facility_consideration_id , 'user_cd' => $user_cd]);
    }

    public function facilityConsiderationConfirmNewMode($facility_consideration,$user_cd,$confirmed_flag)
    {
        $sql = "INSERT INTO t_facility_consideration_confirm (facility_consideration_id,user_cd,confirmed_flag) VALUES (:facility_consideration_id,:user_cd,:confirmed_flag);";
        
        return $this->_create($sql,[
            'facility_consideration_id' => $facility_consideration,
            'user_cd' => $user_cd,
            'confirmed_flag' => $confirmed_flag
        ]);
    }

    public function getInformCategory()
    {
        $sql = "SELECT inform_category_cd FROM m_inform_category WHERE inform_category_name =  :inform_category_name;";
        return $this->_first($sql,[ 'inform_category_name' => '施設注意事項通知']);
    }

    public function updateFacilityConsideration($facility_cd,$facility_consideration_id,$consideration_type,$consideration_contents,$user_cd_login,$user_name_login,$org_cd_login,$org_label_login,$data_system)
    {
        $sql = "
            UPDATE t_facility_consideration
                SET consideration_type = :consideration_type,
                    consideration_contents = :consideration_contents,
                    last_update_datetime = :last_update_datetime,
                    create_user_cd = :create_user_cd,
                    create_user_name = :create_user_name,
                    create_org_cd = :create_org_cd,
                    create_org_label = :create_org_label
            WHERE facility_consideration_id = :facility_consideration_id
            AND facility_cd = :facility_cd;
        ";

        return $this->_update($sql,[
            'facility_cd' => $facility_cd,
            'consideration_type' => $consideration_type,
            'consideration_contents' => $consideration_contents,
            'create_user_cd' => $user_cd_login,
            'create_user_name' => $user_name_login,
            'create_org_cd' => $org_cd_login,
            'create_org_label' => $org_label_login,
            'last_update_datetime' => $data_system,
            'facility_consideration_id' => $facility_consideration_id
        ]);
    }

    public function deleteFacilityConsiderationConfirm($facility_consideration_id,$user_cd)
    {
        $sql = "DELETE FROM t_facility_consideration_confirm WHERE facility_consideration_id = :facility_consideration_id AND user_cd IN ". $this->_buildCommaString($user_cd,true) . ";";
        return $this->_destroy($sql,['facility_consideration_id' => $facility_consideration_id]);
    }

    public function facilityConsiderationConfirmUpdateMode($facility_consideration_id, $user_cd, $confirmed_flag)
    {
        $sql = "
            UPDATE t_facility_consideration_confirm
                SET confirmed_flag = :confirmed_flag,
                    confirmed_datetime = :confirmed_datetime
            WHERE facility_consideration_id = :facility_consideration_id
            AND user_cd = :user_cd;
        ";
        return $this->_update($sql,[
            'confirmed_flag' => $confirmed_flag,
            'confirmed_datetime' => null,
            'facility_consideration_id' => $facility_consideration_id,
            'user_cd' => $user_cd
        ]);
    }
}
