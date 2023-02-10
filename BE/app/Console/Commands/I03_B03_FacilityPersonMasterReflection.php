<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\FacilityUserService;
use Illuminate\Support\Carbon;
use App\Enums\LogBatchJob;
use App\Services\SystemParameterService;
use App\Services\LogService;

class I03_B03_FacilityPersonMasterReflection extends Command
{
    protected $service;
    protected $serviceParameter;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:facility-person-master-reflection {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '■機能概要        
    　施設担当者マスタの反映
    
    ■機能要件
            地区担当履歴マスタおよび飛び地担当履歴マスタから施設担当者マスタを作成する
    
    ■起動パラメータ        
            処理対象日付（形式：｛｛YYYYMMDD｝｝）        
            通常はパラメータ指定なし。このパラメータが指定された場合、当バッチ処理が指定された日付で実行されたものとして振舞う（保守作業用）。
                                                                                                                                                                                                                                                                                                                         
    ■起動サイクル        
            日次
    
    ■戻り値
            正常の場合：0、バリデーションエラーの場合：250、実行エラーの場合：255';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FacilityUserService $service, SystemParameterService $serviceParameter, LogService $serviceAccessLog)
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
        $jobId = 'I03_B03';
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob);
        DB::beginTransaction();
        try {
            ini_set('memory_limit', '-1');
            $inputDate = $this->option('date', '');
            $this->serviceAccessLog->logInput($jobId, $keyBatchJob, ['date' => $inputDate]);
            $isFormatDateOk = true;
            if (!$inputDate) {
                // get data system 
                $dateSystem = getDateSystem();
                if (checkDateValid($dateSystem)) {
                    $inputDate = replaceStringToFormatDate($dateSystem);
                } else {
                    $inputDate = Carbon::now()->format('Y-m-d');
                }
            } else {
                $inputDate = replaceStringToFormatDate($inputDate);
                if (!validateDate($inputDate)) {
                    $this->serviceAccessLog->logInvalidInput($jobId, $keyBatchJob);
                    $isFormatDateOk = false;
                    $this->serviceAccessLog->endJob($jobId, $keyBatchJob);
                    return LogBatchJob::RETURN_PARAMETER_INVALID;
                }
            }
            $this->serviceAccessLog->logTargetDay($jobId, $keyBatchJob, $inputDate);
            if ($isFormatDateOk) {
                // add master equipment history table h_facility_user
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start reflecting h_facility_user data");
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "updated/inserted successfully from h_area_user data to h_facility_user");
                $this->service->addMasterEquipmentHistory($jobId, $keyBatchJob);
                // add master equipment history person table h_facility_user and update end_date table h_facility_user
                $this->service->addMasterEquipmentHistoryFromEnclave($jobId, $keyBatchJob);
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End reflecting h_facility_user data");
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start reflecting h_facility_user data");
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start updating h_facility_user from h_enclave_user data");
                $this->service->updateEndDatePersonChargeArea($jobId, $keyBatchJob);
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End reflecting h_facility_user data");
                // update again data table m_facility_user from table h_facility_user
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start reflecting to m_facility_user master");
                $this->service->changeDataUserFacilityMaster($inputDate, $jobId, $keyBatchJob);
                DB::commit();
            }
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
