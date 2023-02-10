<?php

namespace App\Repositories\UtilizationPointTotal;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Repositories\UtilizationPointTotal\UtilizationPointTotalRepositoryInterface;
use App\Enums\LogBatchJob;
use App\Traits\DateTimeTrait;

class UtilizationPointTotalRepository extends BaseRepository implements UtilizationPointTotalRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = false;
    private $date;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }
    
    public function sumaryPoints()
    {

        $sql = "SELECT receive_user_cd AS user_cd, 
                        point_target_type, SUM(active_point) as active_pointt, 
                        COALESCE('Z04-B01') AS created_by,
                        COALESCE(NULL) AS proxy_created_by,
                        COALESCE('" . $this->date . "') AS created_at,
                        COALESCE('Z04-B01') AS updated_by,
                        COALESCE(NULL) AS proxy_updated_by,
                        COALESCE('" . $this->date . "') AS updated_at 
                FROM t_active_point 
                GROUP BY receive_user_cd, point_target_type;";
        return $this->_all($sql);
    }

    public function deleteTotalPoints($jobId, $keyBatchJob)
    {

        $sql = "DELETE FROM s_active_point";
        return $this->_destroy($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function updateOrInsertTotalPoints($data, $jobId, $keyBatchJob)
    {

        $sql = "
            INSERT INTO 
                s_active_point(
                    user_cd,
                    point_target_type,
                    active_point,
                    created_by,
                    proxy_created_by,
                    created_at,
                    updated_by,
                    proxy_updated_by,
                    updated_at
                )
            VALUES(
                :user_cd,
                :point_target_type,
                :active_pointt,
                :created_by,
                :proxy_created_by,
                :created_at,
                :updated_by,
                :proxy_updated_by,
                :updated_at
            );";
        return $this->_create($sql, (array)$data, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
}
