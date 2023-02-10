<?php

namespace App\Repositories\PersonGroupDetail;

use App\Repositories\BaseRepository;
use App\Repositories\PersonGroupDetail\PersonGroupDetailRepositoryInterface;
use App\Enums\LogBatchJob;

class PersonGroupDetailRepository extends BaseRepository implements PersonGroupDetailRepositoryInterface
{
    public function getPersonGroupDetail()
    {
        $sql = "SELECT T1.facility_cd,T1.person_cd 
                FROM t_select_person_group_detail T1 
                LEFT JOIN m_facility_person T2 ON T1.facility_cd=T2.facility_cd AND T1.person_cd=T2.person_cd
                WHERE T2.facility_cd  IS NULL OR T2.person_cd  IS NULL";
        return $this->_find($sql, []);
    }

    public function deletePersonGroupDetail($facility_cd, $person_cd, $jobId, $keyBatchJob)
    {
        $sql = "DELETE FROM t_select_person_group_detail WHERE facility_cd = :facility_cd AND person_cd = :person_cd";
        return $this->_destroy($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
}
