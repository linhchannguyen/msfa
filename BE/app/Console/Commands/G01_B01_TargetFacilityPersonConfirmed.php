<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TargetFacilityPersonSettingServiceJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\LogBatchJob; // add line
use App\Services\LogService; // add line
use App\Services\SystemParameterService;

class G01_B01_TargetFacilityPersonConfirmed extends Command
{
    protected $service;
    protected $serviceParameter;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:target-facility-individual-confirmation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ターゲット管理で選定されたターゲット施設個人をターゲット品目毎の選定期間を基に確定する。';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TargetFacilityPersonSettingServiceJob $service, SystemParameterService $serviceParameter, LogService $serviceAccessLog)
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
        $jobId = 'G01-B01'; // add line
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob); // add line
        DB::beginTransaction();
        try {
            // Allowed memory size
            ini_set('memory_limit', '-1');
            $dateSystem = $this->serviceParameter->getCurrentSystemDateTime();//date_create($this->serviceParameter->getCurrentSystemDateTime())->format('Y-m-d');
            // update again data table m_user from table i_user
            $this->service->actionTargetFacilityIndividualConfirmation($dateSystem, $jobId, $keyBatchJob);
            $this->serviceAccessLog->endJob($jobId, $keyBatchJob); // add line
            DB::commit();
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
