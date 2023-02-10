<?php

namespace App\Repositories\FacilityGroupDetail;

use App\Repositories\BaseRepository;
use App\Repositories\FacilityGroupDetail\FacilityGroupDetailRepositoryInterface;
use App\Enums\LogBatchJob;
use App\Traits\DateTimeTrait;

class FacilityGroupDetailRepository extends BaseRepository implements FacilityGroupDetailRepositoryInterface
{
    use DateTimeTrait;
    private $date;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function deleteFacilityGroupDetail($jobId, $keyBatchJob)
    {
        $sql = "DELETE FROM t_select_facility_group_detail 
                WHERE facility_cd IN (SELECT T1.facility_cd FROM t_select_facility_group_detail T1 
                                        LEFT JOIN m_facility T2 ON T1.facility_cd = T2.facility_cd 
                                        WHERE T2.facility_cd  IS NULL OR T2.ultmarc_delete_flag = :ultmarc_delete_flag)";
        return $this->_destroy($sql, ['ultmarc_delete_flag' => 1], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
}
