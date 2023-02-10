<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ContactWholesalerMasterReflectionService;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\LogBatchJob;
use App\Services\SystemParameterService;
use App\Services\LogService;
use Illuminate\Support\Carbon;

class I03_B01_WholesalerMasterReflection extends Command
{
    protected $service;
    protected $serviceParameter;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:contact-wholesaler-master-reflection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '■機能概要        
    　施設マスタの卸データへ自社卸マスタのデータを反映する
    
    ■機能要件
    　施設マスタ内の自社卸データを削除し、自社卸マスタから自社卸データを施設マスタに登録する。   
    
    ■起動パラメータ        
            なし
                                                                                                                                                                                                                                                                                                                         
    ■起動サイクル        
            日次
    ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ContactWholesalerMasterReflectionService $service, SystemParameterService $serviceParameter, LogService $serviceAccessLog)
    {
        $this->service = $service;
        $this->serviceParameter = $serviceParameter;
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
        $jobId = 'I03_B01';
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob);
        DB::beginTransaction();
        try {
            // delete data table m_facility where select facility_category_type from m_facility_category where facility_category_name = '卸'
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start  deleting to facility master");
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted facility master successfully from m_facility_user data");
            $this->service->deleteFacility($jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End deleting facility master");
            // add data table m_facility where select facility_category_type from m_contact_wholesaler
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start  inserting m_contact_wholesaler data into m_facility");
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Inserted successfully m_contact_wholesaler data into m_facility");
            $this->service->addFacility($jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End inserting m_contact_wholesaler data into m_facility");
            DB::commit();
            $this->serviceAccessLog->endJob($jobId, $keyBatchJob);
            return LogBatchJob::RETURN_SUCCESS;
        } catch (\Throwable $th) {
            DB::rollBack();
            echo __('auth.system_error') . PHP_EOL;
            Log::error($th);
            $this->serviceAccessLog->endJob($jobId, $keyBatchJob);
            return LogBatchJob::RETURN_FAIL;
        }
    }
}
