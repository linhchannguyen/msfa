<?php

namespace App\Services;

use Carbon\Carbon;
use App\Traits\DateTimeTrait;
use App\Repositories\Stock\StockRepositoryInterface;
use App\Repositories\AttendantManagement\AttendantManagementRepositoryInterface;

class AttendantManagementService
{
    use DateTimeTrait;
    private $repo, $repo_stock;

    public function __construct(AttendantManagementRepositoryInterface $repo, StockRepositoryInterface $repo_stock)
    {
        $this->repo = $repo;
        $this->repo_stock = $repo_stock;
    }

    public function getInfoConvention($params)
    {
        $result['convention_info'] = $this->repo->getInfoConvention($params['convention_id']);
        $result['convention_info'][0]->product_list = json_decode($result['convention_info'][0]->product_list) ?? [];
        $parameter_add_stock = $this->repo->getParameterAddStock();
        $result['stock_type'] = $parameter_add_stock->definition_value ?? "";
        $result['user_login_org_cd'] = $params['org']->org_cd ?? "";
        $result['action_id'] = $params['convention_id'];
        $result['convention_status_item'] = $this->repo->getConventionStatusItem();
        $result['medical_staff'] = $this->repo->getMedicalStaff();
        $result['input_deadline'] = $this->repo->getInputDeadline();
        $result['part_list'] = $this->repo->getVariableDefinitionD14();
        $result['status_item_list'] = $this->repo->getVariableDefinitionD18();
        $other_attendee = $this->repo->getOtherAttendee($params['convention_id']);
        foreach ($other_attendee as $data) {
            $data->other_headcount = empty($data->other_headcount) ? [] : json_decode($data->other_headcount, true);
            if (empty($data->other_headcount)) {
                foreach ($result['convention_status_item'] as $status_item) {
                    $data->other_headcount[] = [
                        "status_item_cd" => $status_item->status_item_cd,
                        "headcount" => 0,
                    ];
                }
            }
        }
        $result['other_attendee'] = $other_attendee;
        return $result;
    }

    public function searchData($conditions)
    {
        //フォロー日付
        $convention_start_date = $this->repo->getStartDateConventionSchedule($conditions->convention_id)->start_date ?? '0000-00-00';
        $convention_follow_up = $this->repo->getLectureFollowUp()->parameter_value ?? 0;
        $convention_start_date_add_convention_follow_up = Carbon::parse($convention_start_date)->addDays($convention_follow_up)->format('Y-m-d');
        $conditions->convention_start_date = $convention_start_date;
        $conditions->convention_start_date_add_convention_follow_up = $convention_start_date_add_convention_follow_up;
        //フォロー日付
        $conditions->status_item_cd_200 = STATUS_ITEM_GUIDE;
        $conditions->status_item_cd_700 = STATUS_ITEM_ATTENDED;
        $result = $this->repo->searchData($conditions);
        foreach ($result['records'] as $data) {
            $data->convention_attendee_status_detail = empty($data->convention_attendee_status_detail) ? [] : json_decode($data->convention_attendee_status_detail, true);
            if (empty($data->convention_attendee_status_detail)) {
                $convention_status_item = $this->repo->getConventionStatusItem();
                foreach ($convention_status_item as $status_item) {
                    $data->convention_attendee_status_detail[] = [
                        "convention_attendee_id" => $data->convention_attendee_id,
                        "status_item_cd" => $status_item->status_item_cd,
                        "status_item_value" => $status_item->status_item_cd == 300 ? "00" : "0",
                        "update_date" => ""
                    ];
                }
            }
            $data->follow_date = empty($data->follow_date) ? [] : json_decode($data->follow_date, true);
            $data->series_convention = empty($data->series_convention) ? [] : json_decode($data->series_convention, true);
            // ・以下の条件で直近の[スケジュール．開始日付]を取得する。
            array_multisort(
                array_column($data->follow_date, 'start_date'),
                SORT_ASC,
                $data->follow_date
            );
            array_multisort(
                array_column($data->series_convention, 'start_date'),
                SORT_DESC,
                $data->series_convention
            );
            $data->series_convention_200 = UN_LAST_TIME_INFORMATION;
            $data->series_convention_700 = UN_LAST_TIME_ATTENDED;
            foreach ($data->series_convention as $serie) {
                if ($serie['start_date'] <= $data->start_date && $data->convention_id != $serie['convention_id']) {
                    /*
                    ◆[講演会．シリーズ講演会ID]ありの場合
                    ・[講演会．シリーズ講演会ID]が同一もしくは[講演会．シリーズ講演会ID]と[講演会．講演会ID]が一致する講演会の中で直近の講演会IDを取得する
                    ・以下の条件に紐づく[講演会出席者状況確認明細．状況確認値]を取得する。
                    　直近の講演会ID＝[講演会出席者．講演会ID]
                    　直近の講演会の[講演会出席者．個人コード]＝[講演会出席者．個人コード]
                    　直近の講演会の[講演会出席者．その他個人フラグ]＝”0”
                    　直近の講演会の[講演会出席者状況確認明細．状況確認項目コード]＝”案内”
                    */
                    if ($serie['status_item_value_200'] >= 1 && $data->person_cd == $serie['person_cd']) {
                        $data->series_convention_200 = LAST_TIME_INFORMATION;
                    }
                    /*◆[講演会．シリーズ講演会ID]ありの場合
                    ・[講演会．シリーズ講演会ID]が同一もしくは[講演会．シリーズ講演会ID]と[講演会．講演会ID]が一致する講演会の中で直近の講演会IDを取得する
                    ・以下の条件に紐づく[講演会出席者状況確認明細．状況確認値]を取得する。
                    　直近の講演会ID＝[講演会出席者．講演会ID]
                    　直近の講演会の[講演会出席者．個人コード]＝[講演会出席者．個人コード]
                    　直近の講演会の[講演会出席者．その他個人フラグ]＝”0”
                    　直近の講演会の[講演会出席者状況確認明細．状況確認項目コード]＝”出欠”
                    */
                    if ($serie['status_item_value_700'] >= 1 && $data->person_cd == $serie['person_cd']) {
                        $data->series_convention_700 = LAST_TIME_ATTENDED;
                    }
                    break;
                }
            }
            unset($data->series_convention);
            /*
            ・以下の条件に当てはまる値を[システム変数マスタ]より取得する
            　設定名 = ”講演会フォロー期間”
            ・以下の条件で直近の[スケジュール．開始日付]を取得する。
            　[講演会．開催日]＜＝[スケジュール．開始日付]＜＝[講演会．開催日]＋講演会フォロー期間
            [スケジュール．活動日付](講演会の開催日)＜＝ [スケジュール．活動日付](面談の実施日)＜＝[スケジュール．活動日付](講演会の開催日)＋講演会フォロー期間】
            　[面談．個人コード]＝[講演会出席者．個人コード]
            　[ディテール．面談内容コード]＝”講演会フォロー”
            　[ディテール．品目コード]＝[講演会品目．品目コード]
            MM/DD形式
            */
            if (!empty($data->follow_date)) {
                $data->follow_date = $this->responseDateTimeFormat($data->follow_date[0]['start_date'], 'm-d');
            } else {
                $data->follow_date = UNFOLLOW;
            }
        }
        return $result;
    }

    public function addData($parameters)
    {
        $convention_info = $this->repo->getInfoConvention($parameters->convention_id);
        $status_type = $convention_info[0]->status_type ?? "";
        $start_date = $convention_info[0]->start_date ?? "0000-00-00";
        $plan_user_cd = $convention_info[0]->plan_user_cd ?? "";
        $plan_org_cd = [];
        if (isset($convention_info[0]->convention_target_org_list)) {
            $plan_org_cd = explode(",", $convention_info[0]->convention_target_org_list);
        }
        $input_deadline = $this->repo->getInputDeadline()->parameter_value;
        $current = $this->currentJapanDateTime('Y-m-d');
        $before_current = date('Y-m-d', strtotime($current . " -$input_deadline month"));
        $roles = $parameters->roles;
        $user_cd = $parameters->user_cd;
        $org_cd = $parameters->org->org_cd ?? "";
        $other_attendee = $parameters->other_attendee ?? [];
        $convention_attendee = $parameters->convention_attendee ?? [];
        $datas['other_attendee'] = [];
        $datas['add_status_detail'] = [];
        $datas['update_status_detail'] = [];
        foreach ($other_attendee as $data) {
            foreach ($data['other_headcount'] as $value) {
                array_push($datas['other_attendee'], [
                    'convention_id' => $parameters->convention_id,
                    'occupation_type' => $data['occupation_type'],
                    'status_item_cd' => $value['status_item_cd'],
                    'headcount' => $value['headcount']
                ]);
            }
        }
        /*
        *◆ログインユーザが講演会実施者権限ありの場合
        *    ◆ログインユーザが講演会の企画者と同じまたは対象組織の所属ユーザ
        *        出席者の追加、出席者情報の編集
        *    ◆上記以外の場合
        *        参照のみ
        */
        if ($start_date >= $before_current) {
            if (in_array(config('role.CONVENTION_IMPLEMENTER.CODE'), $roles) && ($plan_user_cd == $user_cd || in_array($org_cd, $plan_org_cd)) && ($status_type == 10 || $status_type == 30 || $status_type == 40)) {
                $status_detail_insert = [];
                $convention_attendee_delete = [];
                foreach ($convention_attendee as $data) {
                    $convention_attendee_insert = [];
                    $deleteFlag = false;
                    if (!empty($data['convention_attendee_id'])) {
                        if (isset($data['deleteFlag'])) {
                            if ($data['deleteFlag']) {
                                $deleteFlag = true;
                            }
                        }
                        if ($deleteFlag) {
                            array_push($convention_attendee_delete, $data['convention_attendee_id']);
                        } else {
                            $this->repo->updateConventionAttendee([
                                'part_type' => $data['part_type'] ?? null,
                                'convention_attendee_id' => $data['convention_attendee_id']
                            ]);
                            $status_detail = $this->repo->getConventionAttendeeStatusDetail($data['convention_attendee_id'])->record;
                            $status_detail = $status_detail > 0 ? true : false;
                            foreach ($data['convention_attendee_status_detail'] as $value) {
                                if ($status_detail) {
                                    $this->repo->updateStatusDetail([
                                        'convention_attendee_id' => $data['convention_attendee_id'],
                                        'status_item_cd' => $value['status_item_cd'],
                                        'status_item_value' => empty($value['status_item_value']) ? '00' : $value['status_item_value'],
                                        'update_date' => $value['update_date'] ?? null
                                    ]);
                                } else {
                                    array_push($status_detail_insert, [
                                        'convention_attendee_id' => $data['convention_attendee_id'],
                                        'status_item_cd' => $value['status_item_cd'],
                                        'status_item_value' => empty($value['status_item_value']) ? '00' : $value['status_item_value'],
                                        'update_date' => $current
                                    ]);
                                }
                            }
                        }
                    } else {
                        $check_person_exits = $this->repo->getConventionAttende($parameters->convention_id, $data['person_cd'] ?? null);
                        if (isset($check_person_exits->convention_attendee_id)) {
                            return 400;
                        }
                        array_push($convention_attendee_insert, [
                            'convention_id' => $parameters->convention_id,
                            'person_cd' => $data['person_cd'] ?? null,
                            'facility_cd' => $data['facility_cd'],
                            'person_name' => $data['person_name'] ?? null,
                            'person_name_kana' => $data['person_name_kana'] ?? null,
                            'facility_short_name' => $data['facility_short_name'] ?? null,
                            'facility_name_kana' => $data['facility_name_kana'] ?? null,
                            'department_cd' => $data['department_cd'] ?? null,
                            'department_name' => $data['department_name'] ?? null,
                            'display_position_cd' => $data['display_position_cd'] ?? null,
                            'display_position_name' => $data['display_position_name'] ?? null,
                            'other_medical_staff_type' => $data['other_medical_staff_type'] ?? null,
                            'other_person_flag' => $data['other_person_flag'],
                            'part_type' => $data['part_type'] ?? null
                        ]);
                        $this->repo->addConventionAttendee($convention_attendee_insert);
                        $last_convention_attendee = $this->repo->lastInsertedConventionAttendee();
                        foreach ($data['convention_attendee_status_detail'] as $value) {
                            array_push($status_detail_insert, [
                                'convention_attendee_id' => $last_convention_attendee->convention_attendee_id,
                                'status_item_cd' => $value['status_item_cd'],
                                'status_item_value' => empty($value['status_item_value']) ? '00' : $value['status_item_value'],
                                'update_date' => $current
                            ]);
                        }
                    }
                }
                if (!empty($convention_attendee_delete)) {
                    $this->repo->deleteConvention($convention_attendee_delete);
                }
                if (!empty($status_detail_insert)) {
                    $this->repo->addStatusDetail($status_detail_insert);
                }
                $this->repo->deleteHeadcount($parameters->convention_id);
                $this->repo->addHeadcount($datas['other_attendee']);
            }
        }
    }
}
