<?php

namespace App\Repositories\RoleUser;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Repositories\RoleUser\RoleUserRepositoryInterface;
use App\Enums\LogBatchJob;
use App\Traits\DateTimeTrait;

class RoleUserRepository extends BaseRepository implements RoleUserRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = false;
    private $date;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function getTimeSystem()
    {
        $sql = "SELECT * FROM c_system_parameter WHERE parameter_key = :parameter_key";
        return $this->_first($sql, ['parameter_key' => 'システム運用日付']);
    }

    public function deleteRoleUser($jobId, $keyBatchJob)
    {
        $sql = "DELETE FROM m_user_role;";
        return $this->_destroy($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function updateRoleUser($dateSystem, $jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO m_user_role
                SELECT user_cd,role,
                    COALESCE('I03-B04') AS created_by,
                    COALESCE(NULL) AS proxy_created_by,
                    COALESCE('" . $this->date . "') AS created_at,
                    COALESCE('I03-B04') AS updated_by,
                    COALESCE(NULL) AS proxy_updated_by,
                    COALESCE('" . $this->date . "') AS updated_at 
                FROM i_user_role 
                WHERE start_date <= :start_date and end_date >= :end_date
                GROUP BY user_cd,role;";
        return $this->_create($sql, ['start_date' => $dateSystem, 'end_date' => $dateSystem], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
}
