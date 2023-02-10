<?php

namespace App\Repositories\AttendantCollectiveRegistration;

use App\Repositories\BaseRepository;
use App\Repositories\AttendantCollectiveRegistration\AttendantCollectiveRegistrationRepositoryInterface;

class AttendantCollectiveRegistrationRepository extends BaseRepository implements AttendantCollectiveRegistrationRepositoryInterface
{
    protected $useAutoMeta = true;
    

    public function getFacilityCd($facility_list)
    {
        $sql = "SELECT facility_cd,facility_short_name,facility_name_kana FROM m_facility WHERE facility_cd IN ". $this->_buildCommaString($facility_list,true)." GROUP BY facility_cd;";
        return $this->_find($sql,[]);
    }


    public function getPersonCd($person_list)
    {
        $sql = "SELECT person_cd,person_name,person_name_kana FROM m_person WHERE person_cd IN ". $this->_buildCommaString($person_list,true)." GROUP BY person_cd;";
        return $this->_find($sql,[]);
    }

    public function getFacilityPerson($facility_list,$person_list)
    {
        $sql = "SELECT 
                    CONCAT( m_facility_person.facility_cd,',',m_facility_person.person_cd) as facility_person,
                    m_facility_person.department_cd,
                    m_department.department_name,
                    m_facility_person.display_position_cd,
                    m_position.position_name as display_position_name
                FROM m_facility_person
                LEFT JOIN m_department
                    ON m_department.department_cd = m_facility_person.department_cd
                LEFT JOIN m_position
                    ON m_position.position_cd = m_facility_person.display_position_cd
                WHERE m_facility_person.facility_cd IN ". $this->_buildCommaString($facility_list,true) . "
                AND m_facility_person.person_cd IN ". $this->_buildCommaString($person_list,true)."
                GROUP BY m_facility_person.facility_cd,m_facility_person.person_cd;";
        
        return $this->_find($sql,[]);
    }

    public function getRegistration($facility_list,$person_list,$convention_id)
    {
        $sql = "SELECT 
                    CONCAT( t_convention_attendee.facility_cd,',',t_convention_attendee.person_cd) as facility_person
                FROM t_convention_attendee
                WHERE facility_cd IN ". $this->_buildCommaString($facility_list,true) . "
                AND person_cd IN ". $this->_buildCommaString($person_list,true)."
                AND convention_id = :convention_id
                GROUP BY facility_cd,person_cd;";
        
        return $this->_find($sql,["convention_id" => $convention_id]);
    }

    public function import($data_import)
    {
        $sql = "INSERT INTO t_convention_attendee 
                    (convention_id,facility_cd,person_cd,other_person_flag,facility_short_name,facility_name_kana,person_name,person_name_kana,department_cd,department_name,display_position_cd,display_position_name) 
                VALUES :values ;";

        return $this->_bulkCreate($sql,$data_import);
    }

    public function getRegistrationTemp($facility_list,$person_list,$convention_id)
    {
        $sql = "
            SELECT 
                t_convention_attendee.facility_cd,
                t_convention_attendee.person_cd
            FROM t_convention_attendee
            WHERE facility_cd IN ". $this->_buildCommaString($facility_list,true) . "
            AND person_cd IN ". $this->_buildCommaString($person_list,true)."
            AND convention_id = :convention_id
            GROUP BY facility_cd,person_cd;
        ";

        return $this->_find($sql,[ "convention_id" => $convention_id ]);
    }
}
