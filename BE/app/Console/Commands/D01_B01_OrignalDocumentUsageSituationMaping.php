<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\DocumentUsageSituationService;
use App\Enums\LogBatchJob; // add line
use App\Services\LogService; // add line

class D01_B01_OrignalDocumentUsageSituationMaping extends Command
{
    protected $service;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:update-usage-situation-map';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '基本設計書【原本資材使用状況マッピング】';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DocumentUsageSituationService $service, LogService $serviceAccessLog)
    {
        $this->service = $service;
        $this->serviceAccessLog = $serviceAccessLog;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $keyBatchJob = $this->serviceAccessLog->keyBatchJob();
        $jobId = 'D01-B01'; // add line
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob); // add line
        DB::beginTransaction();
        try {
            // Allowed memory size
            ini_set('memory_limit', '-1');
            
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "start batch Job Update UsageSituationMap");
            // empty table s_original_document_usage_situation_map
            $this->service->emptyTableUsageSituationMap($jobId, $keyBatchJob);
            // add data document original into table s_original_document_usage_situation_map
            $this->service->addDocumentOriginalIntoUsageSituationMap($jobId, $keyBatchJob);
            // add data document custom into table s_original_document_usage_situation_map
            $this->service->addDocumentCustomIntoUsageSituationMap($jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  mapping document usage situation");
            DB::commit();
            $this->serviceAccessLog->endJob($jobId, $keyBatchJob); // add line
            return LogBatchJob::RETURN_SUCCESS; // add line
        } catch (\Throwable $th) {
            DB::rollBack();
            echo __('auth.system_error') . PHP_EOL;
            Log::error($th);
            $this->serviceAccessLog->endJob($jobId, $keyBatchJob); // add line
            return LogBatchJob::RETURN_FAIL; // add line
        }
    }
}
