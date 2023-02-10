<?php

namespace App\Services;

use App\Repositories\Home\HomeRepositoryInterface;
use App\Traits\DateTimeTrait;

class HomeService
{
    use DateTimeTrait;
    private $repo;

    public function __construct(HomeRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getScreenData()
    {
        $result['widget'] = $this->repo->widget();
        $result['organization_layer'] = $this->repo->getOrganizationLayer();
        $result['actual_digestion_items'] = $this->repo->getActualDigestedItems();
        return $result;
    }

    public function getMessageList($user_cd, $user_org, $orgs)
    {
        $data = $this->repo->all($user_cd);
        $messages = [];
        $important = [];
        $unread = [];
        foreach ($data as $values) {
            $orgCds = [];
            $tmp = false;
            foreach ($orgs as $value) {
                $this->findLayerOrg($value, $orgCds, $values->release_org_cd, $values->layer_num, $tmp);
            }
            if ($values->release_org_cd) {
                foreach ($user_org as $org_cd) {
                    if (in_array($org_cd->org_cd, $orgCds)) {
                        array_push($messages, $values);
                        if ($values->important_flag) {
                            array_push($important, $values);
                        }
                        if (!$values->message_read) {
                            array_push($unread, $values);
                        }
                        break;
                    }
                }
            } else {
                array_push($messages, $values);
                if ($values->important_flag) {
                    array_push($important, $values);
                }
                if (!$values->message_read) {
                    array_push($unread, $values);
                }
            }
        }
        return [
            'messages' => $messages,
            'important' => $important,
            'unread' => $unread
        ];
    }

    public function flaternDataOrg($node, &$orgCds, $user_org_cd, $user_layer_num, &$tmp)
    {
        if (!in_array($node->org_cd, $orgCds)) {
            array_push($orgCds, $node->org_cd);
        }
        if (isset($node->children)) {
            foreach ($node->children as $value) {
                $this->flaternDataOrg($value, $orgCds, $user_org_cd, $user_layer_num, $tmp);
            }
        }
    }

    public function findLayerOrg($node, &$orgCds, $user_org_cd, $user_layer_num, &$tmp)
    {
        if ($node->org_cd == $user_org_cd && $node->layer_num == $user_layer_num) {
            $this->flaternDataOrg($node, $orgCds, $user_org_cd, $user_layer_num, $tmp);
            return;
        }
        if (isset($node->children)) {
            foreach ($node->children as $value) {
                $this->findLayerOrg($value, $orgCds, $user_org_cd, $user_layer_num, $tmp);
            }
        }
    }

    public function readMessage($data)
    {
        return $this->repo->readMessage($data);
    }

    public function checkRead($data)
    {
        return $this->repo->checkRead($data);
    }

    public function informList($user_cd)
    {
        return $this->repo->informList($user_cd);
    }

    public function countInform($user_cd)
    {
        return $this->repo->countInform($user_cd);
    }

    public function informed($user_cd)
    {
        return $this->repo->informed($user_cd);
    }

    public function getActivityPlan($conditions)
    {
        $result = $this->repo->getActivityPlan($conditions);
        foreach ($result as &$value) {
            if($value->call_list){
                $value->call_attendee = $this->repo->getVisitActivityPlan($value->schedule_id);
            }else{
                $value->call_list = 0;
            }
            if($value->briefing_list){
                $value->briefing_attendee = $this->repo->getBriefingActivityPlan($value->schedule_id);
            }else{
                $value->briefing_list = 0;
            }
            if($value->convention_list){
                $value->convention_attendee = $this->repo->getConventionActivityPlan($value->schedule_id);
            }else{
                $value->convention_list = 0;
            }
            $value->private_list = !empty($value->private_list) ? json_decode($value->private_list, true) : [];
            $value->office_schedule_list = !empty($value->office_schedule_list) ? json_decode($value->office_schedule_list, true) : [];
            $value->interviewer = [];
        }
        return $result;
    }

    public function getExternalUrl()
    {
        $data['external_url_link'] = $this->repo->getExternalUrlLink();
        $data['external_embedded_url'] = $this->repo->getExternalEmbeddedUrl();
        return $data;
    }

    public function getNonSubmissionReport($conditions)
    {
        //システム変数(c_system_parameter)に登録されている
        // 「ホーム：報告未提出確認対象期間」
        // で指定されている日数（当日を1日目とします)の過去日の中で
        // 未提出となっている件数を抽出してください。
        // また過去日を特定する際、カレンダーマスタで休日フラグ(holiday_flag)が
        // 有効となっているものは省いて日数を特定するようにお願いします。
        $period_condition['current'] =  $this->currentJapanDateTime('Y-m-d');
        $period = $this->repo->getPeriodConfirmation();
        $period_condition['period'] = $period->parameter_value ?? 0;
        $calendar_dates = $this->repo->getSetPeriod($period_condition);
        $daily_report_unsubmitted_condition['start_date'] = $calendar_dates[count($calendar_dates) - 1]->calendar_date;
        $daily_report_unsubmitted_condition['end_date'] = $period_condition['current'];
        $daily_report_unsubmitted_condition['user_cd'] = $conditions['user_cd'];
        $daily_report_unsubmitted_condition['report_status_type'] = REPORT_STATUS_TYPE_UNSUBMITTED;
        //A-a-2: 設定された期間内でレポート状況が提出済以降になっていない件数合計を表示する。
        $unsubmitted = $this->repo->getDailyReportUnsubmitted($daily_report_unsubmitted_condition);

        $daily_report_unapproved_condition['request_type'] = REQUEST_TYPE_REPORTS;
        $daily_report_unapproved_condition['status_type'] = APPROVAL_STATUS_PENDING;
        $daily_report_unapproved_condition['status_type_detail'] = APPROVAL_STATUS_PENDING;
        $daily_report_unapproved_condition['user_cd'] = $conditions['user_cd'];
        //A-a-4: レポートに関する承認申請のうち承認申請明細で承認者がログインユーザかつ、ステータス区分が承認待ちとなっている件数を表示する
        $daily_report_unapproved = count($this->repo->getDailyReportUnApproved($daily_report_unapproved_condition));

        $briefing_unapproved_condition['request_type'] = [REQUEST_TYPE_BRIEFING_PLAN, REQUEST_TYPE_BRIEFING_RESULTS];
        $briefing_unapproved_condition['status_type'] = APPROVAL_STATUS_PENDING;
        $briefing_unapproved_condition['status_type_detail'] = APPROVAL_STATUS_PENDING;
        $briefing_unapproved_condition['user_cd'] = $conditions['user_cd'];
        //A-a-5: 説明会に関する承認申請のうち承認申請明細で承認者がログインユーザかつ、ステータス区分が承認待ちとなっている件数を表示する
        $briefing_unapproved = count($this->repo->getBriefingUnApproved($briefing_unapproved_condition));

        $convention_unapproved_condition['request_type'] = [REQUEST_TYPE_CONVENTION_PLAN, REQUEST_TYPE_CONVENTION_RESULTS];
        $convention_unapproved_condition['status_type'] = APPROVAL_STATUS_PENDING;
        $convention_unapproved_condition['status_type_detail'] = APPROVAL_STATUS_PENDING;
        $convention_unapproved_condition['user_cd'] = $conditions['user_cd'];
        //A-a-6: 講演会に関する承認申請のうち承認申請明細で承認者がログインユーザかつ、ステータス区分が承認待ちとなっている件数を表示する
        $convention_unapproved = count($this->repo->getConventionUnApproved($convention_unapproved_condition));

        $knowledge_unapproved_condition['request_type'] = REQUEST_TYPE_KNOWLEDGE;
        $knowledge_unapproved_condition['status_type'] = APPROVAL_STATUS_PENDING;
        $knowledge_unapproved_condition['status_type_detail'] = APPROVAL_STATUS_PENDING;
        $knowledge_unapproved_condition['user_cd'] = $conditions['user_cd'];
        //A-a-7: ナレッジに関する承認申請のうち承認申請明細で承認者がログインユーザかつ、ステータス区分が承認待ちとなっている件数を表示する
        $knowledge_unapproveds = $this->repo->getKnowledgeUnApproved($knowledge_unapproved_condition);
        if (empty($knowledge_unapproveds)) {
            $knowledge_unapproved[0]['total_record'] = 0;
            $knowledge_unapproved[0]['knowledge_id_list'] = "";
        } else {
            $knowledge_unapproved[0]['total_record'] = count($knowledge_unapproveds);
            $knowledge_unapproved[0]['knowledge_id_list'] = implode(',', $knowledge_unapproveds);
        }

        $daily_report_remand_condition['request_type'] = REQUEST_TYPE_REPORTS;
        $daily_report_remand_condition['report_status_type'] = REPORT_STATUS_TYPE_UNSUBMITTED;
        $daily_report_remand_condition['status_type'] = APPROVAL_STATUS_REMAND;
        $daily_report_remand_condition['user_cd'] = $conditions['user_cd'];
        //A-a-9: ログインユーザが提出者のレポートのうち、ステータスが「差戻し」の件数を表示する。
        $daily_report_remand = count($this->repo->getDailyReportRemand($daily_report_remand_condition));

        $briefing_remand_condition['user_cd'] = $conditions['user_cd'];
        //A-a-10: ログインユーザが実施者の説明会のうち、差戻しフラグが有効な件数を表示する。
        $briefing_remand = $this->repo->getBriefingRemand($briefing_remand_condition);

        $convention_remand_condition['user_cd'] = $conditions['user_cd'];
        //A-a-11: ログインユーザが企画者の講演会のうち、差戻しフラグが有効の件数を表示する。
        $convention_remand = $this->repo->getConventionRemand($convention_remand_condition);

        $knowledge_remand_condition['knowledge_status_type'] = KNOWLEDGE_STATUS_TYPE_MAKING;
        $knowledge_remand_condition['user_cd'] = $conditions['user_cd'];
        //A-a-12: ログインユーザが提出者のナレッジのうち、ステータスが「差し戻し中」の件数を表示する。
        $knowledge_remand = $this->repo->getKnowledgeRemand($knowledge_remand_condition);

        //A-a-14: QA不適切報告の解除フラグが1になっていない件数を表示する
        $inappropriate_report = count($this->repo->getInappropriateReport());

        $person_unconfirm_condition['user_cd'] = $conditions['user_cd'];
        //A-a-15: ログインユーザが確認対象となっている注意事項のうち、未確認の件数を表示する
        $person_unconfirm = count($this->repo->getPersonConsiderationUnConfirm($person_unconfirm_condition));

        $facility_unconfirm_condition['user_cd'] = $conditions['user_cd'];
        //A-a-16: ログインユーザが確認対象となっている注意事項のうち、未確認の件数を表示する
        $facility_unconfirm = $this->repo->getFacilityConsiderationUnConfirm($facility_unconfirm_condition);


        $data['unsubmitted'] = $period_condition['period'] - ($unsubmitted[0]->total_record ?? 0); //A-a-2
        $data['unapproved'] = $daily_report_unapproved; //A-a-4
        $data['briefing_unapproved'] = $briefing_unapproved; //A-a-5
        $data['convention_unapproved'] = $convention_unapproved; //A-a-6
        $data['knowledge_unapproved'] = $knowledge_unapproved; //A-a-7
        $data['daily_report_remand'] = $daily_report_remand; //A-a-9
        $data['briefing_remand'] = $briefing_remand[0]->total_record ?? 0; //A-a-10
        $data['convention_remand'] = $convention_remand[0]->total_record ?? 0; //A-a-11
        $data['knowledge_remand'] = [ //A-a-12
            'total_record' => count($knowledge_remand),
            'knowledge_id_list' => implode(',', $knowledge_remand)
        ];
        $data['inappropriate_report'] = $inappropriate_report; //A-a-14
        $data['person_unconfirm'] = $person_unconfirm; //A-a-15
        $data['facility_unconfirm'] = $facility_unconfirm[0]->total_record ?? 0; //A-a-16
        return $data;
    }

    //◆ウィジェット種別が実消化ランキングの場合
    public function actualDigestionRanking($conditions)
    {
        return $this->repo->actualDigestionRanking($conditions); //A-e-2 -> A-e-3
    }

    //◆ウィジェット種別が前同売上実績の場合
    public function sameAsBeforeSalesResults($conditions)
    {
        $results = [];
        $sales_months = $this->repo->getTimeSalesResults($conditions);
        foreach($sales_months as $sales_month){
            $conditions['sales_month'] = $sales_month->sales_month;
            $data = $this->repo->sameAsBeforeSalesResults($conditions);
            $record['sales_month'] = substr_replace($sales_month->sales_month, '-', -2, 0);
            $sales_amount = 0.00;
            $previous_year_sales_amount = 0.00;
            foreach($data as $value){
                $sales_amount += $value->sales_amount;
                $previous_year_sales_amount += $value->previous_year_sales_amount;
            }
            $record['sales_amount'] = $sales_amount;
            $record['previous_year_sales_amount'] = $previous_year_sales_amount;
            if($record['sales_amount'] && $record['previous_year_sales_amount']){
                array_push($results, $record);
            }
        }
        return $results; //A-e-4 -> A-e-7
    }

    //◆ウィジェット種別が実消化進捗状況の場合
    public function actualDigestionProcess($conditions)
    {
        $data = $this->repo->actualDigestionProcess($conditions);
        $result = array_reduce($data, function ($carry, $item) {
            $item = (array)$item;
            if (!isset($carry[$item['actual_sales_product_cd']])) {
                $carry[$item['actual_sales_product_cd']] = [
                    'sales_amount' => $item['sales_amount'],
                    'goal_amount' => $item['goal_amount'],
                    'actual_sales_product_cd' => $item['actual_sales_product_cd'],
                    'actual_sales_product_name' => $item['actual_sales_product_name']
                ];
            } else {
                $carry[$item['actual_sales_product_cd']]['sales_amount'] += $item['sales_amount'];
                $carry[$item['actual_sales_product_cd']]['goal_amount'] += $item['goal_amount'];
            }
            return $carry;
        });
        return array_values($result ?? []); //A-e-8 -> A-e-10
    }
}
