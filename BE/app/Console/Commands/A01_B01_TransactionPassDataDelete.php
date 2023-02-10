<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RemoveTransactionPastData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\SystemParameterService;
use App\Enums\LogBatchJob;
use App\Services\LogService;

class A01_B01_TransactionPassDataDelete extends Command
{
    protected $service;
    protected $serviceParameter;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:pass-data-transaction-delete {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '■機能概要        
    　各トランザクションデータに対して、それぞれのデータ種別ごとに設定した期間を過ぎたデータを削除する。
    
    ■機能要件
            保持期間（3年を想定）の切れたデータの削除を行う。
            削除対象のデータは以下の通り。
            ・活動関連データ（面談、説明会、講演会、社内予定、プライベート）
            ・操作ログ
            ・メッセージ
            ・通知
            ・ストック
    
    ■起動パラメータ        
            処理対象日付        
            通常はパラメータ指定なし。このパラメータが指定された場合、当バッチ処理が指定された日付で実行されたものとして振舞う（保守作業用）。
                                                                                                                                                                                                                                                                                                                         
    ■起動サイクル        
            月次';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RemoveTransactionPastData $service, SystemParameterService $serviceParameter, LogService $serviceAccessLog)
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
        $jobId = 'A01-B01';
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob);
        DB::beginTransaction();
        try {
            ini_set('memory_limit', '-1');
            $inputDate = $this->option('date', '');
            $this->serviceAccessLog->logInput($jobId, $keyBatchJob, ['date' => $inputDate]);
            $isFormatDateOk = true;
            if (!$inputDate) {
                $date_system_parameter = getDateSystem();
                if (isset($date_system_parameter->parameter_value)) {
                    $inputDate = replaceStringToFormatDate($date_system_parameter->parameter_value);
                } else {
                    $inputDate = date('Y-m-d', strtotime($now));
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
            if ( $isFormatDateOk ) {
                // 面談過去データ削除/ delete past data interview
                $this->service->deletePassDataInterview($inputDate, $jobId, $keyBatchJob);
                // 説明会過去データ削除/ delete past data tutorial
                $this->service->deletePassDataTutorial($inputDate, $jobId, $keyBatchJob);
                // 講演会過去データ削除/ delete past data present
                $this->service->deletePassDataPresent($inputDate, $jobId, $keyBatchJob);
                // その他イベント過去データ削除/ delete past data other event
                $this->service->deletePassDataOtherEvent($inputDate, $jobId, $keyBatchJob);
                // メッセージ過去データ削除/ delete message past data
                $this->service->deletePassDataMessage($inputDate, $jobId, $keyBatchJob);
                // 通知過去データ削除/ delete notification past data
                $this->service->deletePassDataNotification($inputDate, $jobId, $keyBatchJob);
                // ストック過去データ削除/ delete stock past data
                $this->service->deletePassDataStock($inputDate, $jobId, $keyBatchJob);
                // 操作ログ過去データ削除/ delete operation log past data
                $this->service->deletePassDataOperationLog($inputDate, $jobId, $keyBatchJob);
            }
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
