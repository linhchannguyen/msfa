<?php

namespace App\Console\Commands;

use Exception;
use Carbon\Carbon;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\LogBatchJob; // add line
use App\Services\LogService; // add line
use App\Services\SelectPersonGroupDetailService;
use App\Services\SelectFacilityGroupDetailService;
use App\Services\SystemParameterService; // add line

class I03_B05_SelectListMaintenance extends Command
{
    private $facility_service, $person_service;
    protected $serviceParameter;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:select-list-maintenace';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
            ■機能概要        
        　一定期間内に医師異動が発生している施設個人を判定する

        ■機能要件
        　一定期間内に医師異動が発生している施設個人を判定する

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
    public function __construct(SelectFacilityGroupDetailService $facility_service, SelectPersonGroupDetailService $person_service, SystemParameterService $serviceParameter, LogService $serviceAccessLog)
    {
        $this->facility_service = $facility_service;
        $this->person_service = $person_service;
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
        $jobId = 'I03-B05'; // add line
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob); // add line
        DB::beginTransaction();
        try {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start  select facility maintenance");
            // Delete facility_cd from t_select_facility_group_detail where facility_cd from m_facility
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleting t_select_facility_group_detail data successfully");
            $this->facility_service->deleteFacilityGroupDetail($jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  select facility maintenance");
            // Delete facility_cd and person_cd from t_select_person_group_detail where facility_cd and person_cd from m_facility_person
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start  select facility person maintenance");
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleting t_select_facility_group_detail data successfully");
            $this->person_service->deletePersonGroupDetail($jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  select facility person maintenance");
            DB::commit();
            $this->serviceAccessLog->endJob($jobId, $keyBatchJob); // add line
            return LogBatchJob::RETURN_SUCCESS; // add line
        } catch (Exception $e) {
            DB::rollBack();
            echo __('auth.system_error') . PHP_EOL;
            Log::error($e);
            $this->serviceAccessLog->endJob($jobId, $keyBatchJob); // add line
            return LogBatchJob::RETURN_FAIL; // add line
        }
    }
}
