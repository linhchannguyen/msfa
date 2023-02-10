<?php

namespace App\Services;
use App\Services\LogService;
use App\Repositories\DocumentUsageSituation\DocumentUsageSituationRepositoryInterface;

class DocumentUsageSituationService {
    private $repo, $serviceAccessLog;

    public function __construct(DocumentUsageSituationRepositoryInterface $repo, LogService $serviceAccessLog)
    {
        $this->repo = $repo;
        $this->serviceAccessLog = $serviceAccessLog;
    }
    /**
     * add data document original into table s_original_document_usage_situation_map
     * 
     * @author huynh
     */
    public function addDocumentOriginalIntoUsageSituationMap ($jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Inserted original document  review data   into  s_original_document_usage_situation_map");
        return $this->repo->addDocumentOriginalIntoUsageSituationMap($jobId, $keyBatchJob);
    }

    /**
     * add data document custom into table s_original_document_usage_situation_map
     * 
     * @author huynh
     */
    public function addDocumentCustomIntoUsageSituationMap ($jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Inserted custom document  review data into  s_original_document_usage_situation_map");
        return $this->repo->addDocumentCustomIntoUsageSituationMap($jobId, $keyBatchJob);
    }
    /**
     * empty table s_original_document_usage_situation_map
     * 
     * @author huynh
     */
    public function emptyTableUsageSituationMap ($jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Deleted all data in s_original_document_usage_situation_map");
        return $this->repo->emptyTableUsageSituationMap($jobId, $keyBatchJob);
    }
    
}