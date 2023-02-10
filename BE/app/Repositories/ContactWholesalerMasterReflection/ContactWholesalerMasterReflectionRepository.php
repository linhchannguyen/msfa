<?php

namespace App\Repositories\ContactWholesalerMasterReflection;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Repositories\ContactWholesalerMasterReflection\ContactWholesalerMasterReflectionRepositoryInterface;
use App\Enums\LogBatchJob;
use App\Traits\DateTimeTrait;

class ContactWholesalerMasterReflectionRepository extends BaseRepository implements ContactWholesalerMasterReflectionRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = false;
    private $date, $wholesale;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
        $this->wholesale = '卸';
        $this->wholesale_prefix = '卸接頭辞';
        $this->wholesale_master = '卸マスタ反映';
    }

    public function deleteFacility($jobId, $keyBatchJob)
    {
        $sql = "DELETE FROM m_facility WHERE facility_cd like
                CONCAT((select parameter_value from c_system_parameter where parameter_name = :parameter_name and parameter_key = :parameter_key),'%') ";
        return $this->_destroy($sql, ['parameter_name' => $this->wholesale_master, 'parameter_key' => $this->wholesale_prefix], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    public function addFacility($jobId, $keyBatchJob)
    {
        $sql = "INSERT INTO m_facility(
                    facility_cd,
                    facility_category_type,
                    facility_name,
                    facility_short_name,
                    facility_name_kana,
                    facility_short_name_kana,
                    facility_type,
                    post_number,
                    prefecture_cd,
                    municipality_cd,
                    address,
                    address_kana,
                    phone,
                    created_by,
                    proxy_created_by,
                    created_at,
                    updated_by,
                    proxy_updated_by,
                    updated_at
                    )
                    SELECT
                    CONCAT((SELECT parameter_value FROM c_system_parameter WHERE parameter_key = :wholesale_prefix and parameter_name = :wholesale_master),wholesaler_cd) as facility_cd,
                    (SELECT facility_category_type FROM m_facility_category WHERE facility_category_name = :wholesale) as facility_category_type,
                    wholesaler_name as facility_name,
                    wholesaler_name_kana as facility_name_kana,
                    wholesaler_short_name as facility_short_name,
                    wholesaler_short_name_kana as facility_short_name_kana,
                    wholesaler_type as facility_type,
                    post_number,
                    prefecture_cd,
                    municipality_cd,
                    address,
                    address_kana,
                    phone,
                    COALESCE('I03-B01') AS created_by,
                    COALESCE(NULL) AS proxy_created_by,
                    COALESCE('" . $this->date . "') AS created_at,
                    COALESCE('I03-B01') AS updated_by,
                    COALESCE(NULL) AS proxy_updated_by,
                    COALESCE('" . $this->date . "') AS updated_at 
                    FROM m_contact_wholesaler
                    WHERE import_facility_flag = 1";
        $parram = [
            'wholesale' => $this->wholesale,
            'wholesale_prefix' => $this->wholesale_prefix,
            'wholesale_master' => $this->wholesale_master
        ];
        return $this->_create($sql, $parram, LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
}
