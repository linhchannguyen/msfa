<?php

namespace App\Repositories\DocumentUsageSituation;

use App\Repositories\BaseRepository;
use App\Repositories\DocumentUsageSituation\DocumentUsageSituationRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Services\SystemParameterService;
use App\Enums\LogBatchJob;

class DocumentUsageSituationRepository extends BaseRepository implements DocumentUsageSituationRepositoryInterface
{
    protected $system;
    public function __construct(SystemParameterService $system)
    {
        $this->system = $system;
    }
    /**
     * add data document original into table s_original_document_usage_situation_map
     * 
     * @author huynh
     */
    public function addDocumentOriginalIntoUsageSituationMap ($jobId, $keyBatchJob) {
        $sql = "INSERT INTO s_original_document_usage_situation_map (usage_situation_id, original_document_id, 
                created_by, proxy_created_by, created_at, updated_by, proxy_updated_by, updated_at) 
                SELECT T1.usage_situation_id, T3.document_id AS original_document_id, 
                    COALESCE('D01-B01') AS created_by,
                    COALESCE(NULL) AS proxy_created_by,
                    COALESCE('" . date_format(date_create($this->system->getCurrentSystemDateTime()), 'YmdHis') . "') AS created_at,
                    COALESCE('D01-B01') AS updated_by,
                    COALESCE(NULL) AS proxy_updated_by,
                    COALESCE('" . date_format(date_create($this->system->getCurrentSystemDateTime()), 'YmdHis') . "') AS updated_at
                FROM t_document_review T1 
                JOIN t_document T2 ON T1.document_id = T2.document_id 
                JOIN t_original_document_detail T3 ON T3.document_id = T2.document_id 
                JOIN m_document_category M1 ON M1.document_category_cd = T3.document_category_cd 
                WHERE M1.cover_flag = 0 AND T2.document_type = (SELECT definition_value FROM m_variable_definition WHERE definition_label='原本資材' LIMIT 1)";
        $this->_create($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }

    /**
     * add data document custom into table s_original_document_usage_situation_map
     * 
     * @author huynh
     */
    public function addDocumentCustomIntoUsageSituationMap ($jobId, $keyBatchJob) {
        //Q&A 207: remove M1.cover_flag = 0
        $sql = "INSERT INTO s_original_document_usage_situation_map (usage_situation_id, original_document_id, 
                created_by, proxy_created_by, created_at, updated_by, proxy_updated_by, updated_at)
                SELECT T1.usage_situation_id, T3.original_document_id, 
                    COALESCE('D01-B01') AS created_by,
                    COALESCE(NULL) AS proxy_created_by,
                    COALESCE('" . date_format(date_create($this->system->getCurrentSystemDateTime()), 'YmdHis') . "') AS created_at,
                    COALESCE('D01-B01') AS updated_by,
                    COALESCE(NULL) AS proxy_updated_by,
                    COALESCE('" . date_format(date_create($this->system->getCurrentSystemDateTime()), 'YmdHis') . "') AS updated_at 
                FROM t_document_review T1 
                JOIN t_document T2 ON T1.document_id = T2.document_id 
                JOIN t_document_composition T3 ON T3.document_id = T2.document_id 
                JOIN t_original_document_detail T4 ON T4.document_id = T3.original_document_id
                WHERE T2.document_type = (SELECT definition_value FROM m_variable_definition WHERE definition_label='カスタム資材' LIMIT 1)
                GROUP BY T1.usage_situation_id, T3.original_document_id, T2.document_id";
        $this->_create($sql, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
    }
    /**
     * empty table s_original_document_usage_situation_map
     * 
     * @author huynh
     */
    public function emptyTableUsageSituationMap ($jobId, $keyBatchJob) {
        $sql1 = "SET FOREIGN_KEY_CHECKS = 0";
        DB::select($sql1);
        $sql2 = "DELETE FROM s_original_document_usage_situation_map";
        $this->_destroy($sql2, [], LogBatchJob::TYPE_BATCH_JOB, $jobId, $keyBatchJob);
        $sql3 = "SET FOREIGN_KEY_CHECKS = 1";
        DB::select($sql3);
    }

}
