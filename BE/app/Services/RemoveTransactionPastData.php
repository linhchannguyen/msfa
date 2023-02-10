<?php
namespace App\Services;

use App\Repositories\FacilityUser\FacilityUserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\LogService; // add line

class RemoveTransactionPastData {
    private $repo;
    protected $serviceAccessLog;

    public function __construct(FacilityUserRepositoryInterface $repo, LogService $serviceAccessLog)
    {
        $this->repo = $repo;
        $this->serviceAccessLog = $serviceAccessLog;
    }
    /**
     * [BE][A01-B01] トランザクション過去データ削除 Transaction past data remove -2. 面談過去データ削除/ delete past data interview
     * 
     * @param date : time setting to delete
     * @author huynh
     */
    public function deletePassDataInterview ($date, $jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start deleting call data");
        // get data exchange data retention time
        $datePassData = $this->timePassData('面談データ保持期間(月)', $date);
        // if isset data then delete data relation
        if ( $datePassData ) {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of call (面談保持期間) " . $datePassData);
            $this->repo->deleteExchangeDataRetentionTime ($datePassData, $jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted successfully call data");
        } else {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Executing Day doesn't exist");
        }
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End deleting call data");
    }
    /**
     * [BE][A01-B01] トランザクション過去データ削除 Transaction past data remove -3. 説明会過去データ削除/ delete past data tutorial
     * 
     * @param date : time setting to delete
     * @author huynh
     */
    public function deletePassDataTutorial ($date, $jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start deleting briefing data");
        // get data exchange data retention time
        $datePassData = $this->timePassData('説明会データ保持期間(月)', $date);
        // if isset data then delete data relation
        if ( $datePassData ) {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of briefing (説明会保持期間) " . $datePassData);
            $this->repo->deleteDataTutorial($datePassData, $jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted successfully briefing data");
        } else {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of briefing data doesn't exist");
        }
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End deleting briefing data");
    }
    /**
     * [BE][A01-B01] トランザクション過去データ削除 Transaction past data remove -4. 講演会過去データ削除/ delete past data present
     * 
     * @param date : time setting to delete
     * @author huynh
     */
    public function deletePassDataPresent ($date, $jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start deleting convention data");
        // get data exchange data retention time
        $datePassData = $this->timePassData('講演会データ保持期間(月)', $date);
        // if isset data then delete data relation
        if ( $datePassData ) {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of convention data (講演会保持期間) " . $datePassData);
            $this->repo->deleteDataPresent($datePassData, $jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted successfully convention data");
        } else {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of convention data doesn't exist");
        }
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End deleting convention data");
    }

    /**
     * [BE][A01-B01] トランザクション過去データ削除 Transaction past data remove -5. その他イベント過去データ削除/ delete past data other event
     * 
     * @param date : time setting to delete
     * @author huynh
     */
    public function deletePassDataOtherEvent ($date, $jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start deleting other events data");
        // get data exchange data retention time
        $datePassData = $this->timePassData('その他イベントデータ保持期間(月)', $date);
        // if isset data then delete data relation
        if ( $datePassData ) {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of other events data (その他イベントデータ保持期間) " . $datePassData);
            $this->repo->deleteDataOtherEvent($datePassData, $jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted successfully other events data");
        } else {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of other events data doesn't exist");
        }
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  deleting other events data");
    }

    /**
     * [BE][A01-B01] トランザクション過去データ削除 Transaction past data remove -6. メッセージ過去データ削除/ delete message past data 
     * 
     * @param date : time setting to delete
     * @author huynh
     */
    public function deletePassDataMessage ($date, $jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start deleting message data");
        // get data exchange data retention time
        $datePassData = $this->timePassData('メッセージデータ保持期間(月)', $date);
        // if isset data then delete data relation
        if ( $datePassData ) {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of message data(メッセージデータ保持期間)" . $datePassData);
            $this->repo->deleteDataMessage($datePassData, $jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted successfully message data");
        } else {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of message data doesn't exist");
        }
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  deleting message data");
    }

    /**
     * [BE][A01-B01] トランザクション過去データ削除 Transaction past data remove -7. 通知過去データ削除/ delete notification past data 
     * 
     * @param date : time setting to delete
     * @author huynh
     */
    public function deletePassDataNotification ($date, $jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start deleting inform data");
        // get data exchange data retention time
        $datePassData = $this->timePassData('通知データ保持期間(月)', $date);
        // if isset data then delete data relation
        if ( $datePassData ) {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of inform data(通知データ保持期間)" . $datePassData); // spec thieu
            $this->repo->deleteDataNotification($datePassData, $jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted successfully inform data");
        } else {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of inform data doesn't exist");
        }
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  deleting inform data"); // spec thieu
    }

    /**
     * [BE][A01-B01] トランザクション過去データ削除 Transaction past data remove -8. ストック過去データ削除/ delete stock past data 
     * 
     * @param date : time setting to delete
     * @author huynh
     */
    public function deletePassDataStock ($date, $jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start deleting Stock data");
        // get data exchange data retention time
        $datePassData = $this->timePassData('ストックデータ保持期間(月)', $date);
        // if isset data then delete data relation
        if ( $datePassData ) {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of stock data(ストックデータ保持期間(月))" . $datePassData);
            $this->repo->deleteDataStock($datePassData, $jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted successfully stock data");
        } else {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of stock data doesn't exist");
        }
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  deleting Stock data"); // spec thieu
    }

    /**
     * [BE][A01-B01] トランザクション過去データ削除 Transaction past data remove -9. 操作ログ過去データ削除/ delete operation log past data 
     * 
     * @param date : time setting to delete
     * @author huynh
     */
    public function deletePassDataOperationLog ($date, $jobId, $keyBatchJob) {
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "Start deleting access data");
        // get data exchange data retention time
        $datePassData = $this->timePassData('操作ログデータ保持期間(月)', $date);
        // if isset data then 
        if ( $datePassData ) {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of access data(操作ログデータ保持期間(月))" . $datePassData);
            // out file data before delete
            $this->repo->outputDataToFileOperationLog($datePassData, $jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "saved access data successfully ");
            // delete data relation
            $this->repo->deleteDataOperationLog($datePassData, $jobId, $keyBatchJob);
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "deleted successfully access data");
        } else {
            $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "retention period (month) of access data doesn't exist");
        }
        $this->serviceAccessLog->logInfoContent($jobId, $keyBatchJob, "End  deleting access data");
    }

    protected function timePassData ($textSystem, $date) {
        $retention = $this->repo->getExchangeDataRetentionTime($textSystem);
        if ( count($retention) ) {
            $amountMonthPassData = (int)$retention[0]->parameter_value > 0 ? (int)$retention[0]->parameter_value : 0;
            if ($amountMonthPassData) {
                return Carbon::parse($date)->subMonths($amountMonthPassData)->format('Y-m-d');
            }
        }
        return 0;
    }
}