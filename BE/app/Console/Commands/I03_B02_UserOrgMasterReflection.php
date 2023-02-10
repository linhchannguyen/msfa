<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\OrganizationManagementService;
use Illuminate\Support\Carbon;
use App\Enums\LogBatchJob; // add line
use App\Services\SystemParameterService; // add line
use App\Services\LogService; // add line

class I03_B02_UserOrgMasterReflection extends Command
{
    protected $service;
    protected $serviceParameter;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:update-user-org-master-reflection {--date=}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '■機能概要        
    　ユーザ・組織の当日データを作成する
    
    ■機能要件
    　＋ユーザ履歴データから当日データを作成する
    　＋組織履歴データから当日データを作成する
    　＋所属履歴データから当日データを作成する
    
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
    public function __construct(OrganizationManagementService $service, SystemParameterService $serviceParameter, LogService $serviceAccessLog)
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
        $jobId = 'I03-B02'; // add line
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob); // add line
        DB::beginTransaction();
        try {
            // Allowed memory size
            ini_set('memory_limit', '-1');
            $inputDate = $this->option('date', ''); // if has exist
            $this->serviceAccessLog->logInput($jobId, $keyBatchJob, ['date' => $inputDate]); // add line
            $isFormatDateOk = true;
            if ( !$inputDate ) {
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
            if ( $isFormatDateOk ) {
                // update again data table m_user from table i_user
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start  reflecting to user master");
                $this->service->excuteUserMaster($inputDate, $jobId, $keyBatchJob);
                // update again data table m_org from table i_org
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start reflecting to m_org master");
                $this->service->excuteOrganizationMaster($inputDate, $jobId, $keyBatchJob);
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End reflecting to user master");
                // update again data table m_user_org from table i_user_org
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start reflecting to m_user_org master");
                $this->service->excuteUserOrganizationMaster($inputDate, $jobId, $keyBatchJob);
                $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End reflecting to m_user_org master");
            }
            DB::commit();
            $this->serviceAccessLog->endJob($jobId, $keyBatchJob); // add line
            return LogBatchJob::RETURN_SUCCESS; // add line
        } catch (\Throwable $th) {
            DB::rollBack();
            echo __('auth.system_error') . PHP_EOL; // add line
            Log::error($th); // add line
            $this->serviceAccessLog->endJob($jobId, $keyBatchJob); // add line
            return LogBatchJob::RETURN_FAIL; // add line
        }
    }
}
