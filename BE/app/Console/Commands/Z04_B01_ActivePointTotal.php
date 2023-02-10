<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\UtilizationPointTotalService;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\LogBatchJob;
use App\Services\SystemParameterService;
use App\Services\LogService;
use Illuminate\Support\Carbon;

class Z04_B01_ActivePointTotal extends Command
{
    protected $service;
    protected $serviceParameter;
    protected $serviceAccessLog;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:active-point-total';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '■機能概要        
    当システムの各機能から登録された活用度ポイント数をユーザ・コンテンツ単位で集計する。

    ■機能要件
    以下のアクションで登録された活用度ポイントを日次でユーザー別に集計すること
    　・[ナレッジ]ナレッジが公開された
    　・[ナレッジ]ナレッジが承認された
    　・[ナレッジ]いいねが登録された
    　・[ナレッジ]コメントを登録した
    　・[QA]質問を登録した
    　・[QA]ベストアンサーに選ばれた
    　・[QA]回答を登録した
    　・[資材]レビューを登録した
    　・[資材]カスタム資材をコピーされた';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UtilizationPointTotalService $service, SystemParameterService $serviceParameter, LogService $serviceAccessLog)
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
        $jobId = 'Z04-B01';
        $this->serviceAccessLog->startJob($jobId, $keyBatchJob);
        DB::beginTransaction();
        try {
            // update summary points
            $this->service->sumaryPoints($jobId, $keyBatchJob);
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
