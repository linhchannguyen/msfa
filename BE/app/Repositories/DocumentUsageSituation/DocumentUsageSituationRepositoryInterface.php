<?php

namespace App\Repositories\DocumentUsageSituation;

use App\Repositories\BaseRepositoryInterface;

interface DocumentUsageSituationRepositoryInterface extends BaseRepositoryInterface
{
    public function addDocumentOriginalIntoUsageSituationMap ($jobId, $keyBatchJob);
    public function addDocumentCustomIntoUsageSituationMap ($jobId, $keyBatchJob);
    public function emptyTableUsageSituationMap ($jobId, $keyBatchJob);
}
