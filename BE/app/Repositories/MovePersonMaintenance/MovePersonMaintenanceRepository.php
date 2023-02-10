<?php

namespace App\Repositories\MovePersonMaintenance;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Repositories\MovePersonMaintenance\MovePersonMaintenanceRepositoryInterface;
use App\Enums\LogBatchJob;
use App\Traits\DateTimeTrait;

class MovePersonMaintenanceRepository extends BaseRepository implements MovePersonMaintenanceRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = false;
    private $date;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d');
    }

    public function getSystemParameter()
    {
        $sql = "SELECT * FROM c_system_parameter WHERE parameter_key = :parameter_key AND parameter_name = :parameter_name";
        return $this->_first($sql, ['parameter_key' => '異動判定期間', 'parameter_name' => '異動医師メンテナンス']);
    }

    public function updateMasterFacilityPerson($jobId, $keyBatchJob)
    {
        $sql = "UPDATE m_facility_person SET move_flag = :move_flag";
        return $this->_update($sql, ['move_flag' => 0], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function getHistoryFacilityPerson($input_date, $parameter_value, $jobId, $keyBatchJob)
    {
        $date = $input_date ? $input_date : $this->date;
        if ($parameter_value) {
            $end_date = date('Y-m-d', strtotime("-" . $parameter_value . " days", strtotime($date)));
        } else {
            $end_date = $date;
        }
        $sql = "INSERT INTO m_facility_person(person_cd,facility_cd,department_cd,display_position_cd,academic_position_cd,move_flag,created_by,proxy_created_by,created_at,updated_by,proxy_updated_by,updated_at)
                SELECT person_cd,
                            facility_cd,
                            department_cd,
                            position_cd AS display_position_cd,
                            academic_position_cd,
                            COALESCE(1)  AS move_flag,
                            COALESCE('I03-B06') AS created_by,
                            COALESCE(NULL) AS proxy_created_by,
                            COALESCE('" . $this->date . "') AS created_at,
                            COALESCE('I03-B06') AS updated_by,
                            COALESCE(NULL) AS proxy_updated_by,
                            COALESCE('" . $this->date . "') AS updated_at 
                FROM h_facility_person
                WHERE end_date <= :start_date AND end_date >= :end_date
                ON DUPLICATE KEY UPDATE move_flag = VALUES(move_flag), updated_by = VALUES(updated_by), proxy_updated_by = VALUES(proxy_updated_by), updated_at = VALUES(updated_at)";
        return $this->_find($sql, ['start_date' => $this->date, 'end_date' => $end_date], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
}
