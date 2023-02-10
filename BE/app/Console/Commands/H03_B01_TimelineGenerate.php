<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TimelineGenerationService;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Enums\LogBatchJob;
use App\Services\SystemParameterService;
use App\Services\LogService;

class H03_B01_TimelineGenerate extends Command
{
    protected $service;
    protected $serviceParameter;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:timeline-generation {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '■機能概要        
    　入力期間内の面談・説明会・講演会データからタイムライン明細データを生成する。
    　面談はディテール単位、説明会・講演会は施設個人単位でデータを生成する。
    　[タイムライン]はナレッジとの紐付けが存在するため洗い替えは行わず、新規追加データの登録、更新、削除を行う。
    　[タイムライン品目]・[タイムライン実施者]は入力期間内のデータを全件洗い替える。
    ■機能要件
            1-外部連携データが存在する場合、そのデータからタイムライン明細データを生成できること
            2-※外部連携データはエンドユーザからの提供データとなるため、一次では実装対象外
    
    ■起動パラメータ        
            処理対象日付        
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
    public function __construct(TimelineGenerationService $service, SystemParameterService $serviceParameter, LogService $serviceAccessLog)
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
        $jobId = 'H03_B01';
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob);
        DB::beginTransaction();
        try {
            // Allowed memory size
            ini_set('memory_limit', '-1');
            $inputDate = $this->option('date', '');
            $this->serviceAccessLog->logInput($jobId, $keyBatchJob, ['date' => $inputDate]);
            $isFormatDateOk = true;
            if (!$inputDate) {
                $date_system_parameter = $this->service->getSystemParameterDate();
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
            $inputDate = date("Y-m-01", strtotime($inputDate));
            $this->serviceAccessLog->logTargetDay($jobId, $keyBatchJob, $inputDate);
            if ($isFormatDateOk) {
                // update again data table m_user_org
                $this->service->updateTimelineGeneration($inputDate, $jobId, $keyBatchJob);
            }
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
