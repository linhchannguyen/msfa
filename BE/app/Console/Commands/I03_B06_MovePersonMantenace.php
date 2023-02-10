<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MovePersonMaintenanceService;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Enums\LogBatchJob; // add line
use App\Services\SystemParameterService; // add line
use App\Services\LogService; // add line

class I03_B06_MovePersonMantenace extends Command
{
    protected $serviceParameter;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:move-person-maintenance {--date=}';

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
                処理対象日付（形式：｛｛YYYYMMDD｝｝）        
                通常はパラメータ指定なし。このパラメータが指定された場合、当バッチ処理が指定された日付で実行されたものとして振舞う（保守作業用）。

        ■起動サイクル        
            日次
    ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MovePersonMaintenanceService $service, SystemParameterService $serviceParameter, LogService $serviceAccessLog)
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
        $now = $this->serviceParameter->getCurrentSystemDateTime();
        $jobId = 'I03-B04'; // add line
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob); // add line
        DB::beginTransaction();
        try {
            // Allowed memory size
            ini_set('memory_limit', '-1');
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "start batch Job Move Person Maintenance");
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Move Person Maintenance");
            $inputDate = $this->option('date', '');
            $this->serviceAccessLog->logInput($jobId, $keyBatchJob, ['date' => $inputDate]); // add line
            $isFormatDateOk = true;
            if (!$inputDate) {
                // get data system 
                $dateSystem = getDateSystem();
                if (checkDateValid($dateSystem)) {
                    $inputDate = replaceStringToFormatDate($dateSystem);
                } else {
                    $inputDate = date('Y-m-d', strtotime($now));
                }
            } else {
                $inputDate = replaceStringToFormatDate($inputDate);
                if (!validateDate($inputDate)) {
                    $this->serviceAccessLog->logInvalidInput($jobId, $keyBatchJob); // add line
                    $isFormatDateOk = false;
                    $this->serviceAccessLog->endJob($jobId, $keyBatchJob); // add line
                    return LogBatchJob::RETURN_PARAMETER_INVALID; // add line
                }
            }
            $this->serviceAccessLog->logTargetDay($jobId, $keyBatchJob, $inputDate); // add line
            if ($isFormatDateOk) {
                // update again data table m_facility_person from table h_facility_person
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start moving facility person");
                $this->service->updateTransferDoctor($inputDate, $jobId, $keyBatchJob);
                DB::commit();
            }
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
