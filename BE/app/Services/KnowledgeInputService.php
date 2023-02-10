<?php

namespace App\Services;

use App\Repositories\KnowledgeInput\KnowledgeInputRepositoryInterface;
use App\Services\SystemParameterService;
use App\Repositories\Inform\InformRepositoryInterface;
use App\Traits\ArrayTrait;

class KnowledgeInputService
{
    use ArrayTrait;
    private $repo;

    public function __construct(KnowledgeInputRepositoryInterface $repo, SystemParameterService $system, InformRepositoryInterface $inform)
    {
        $this->repo = $repo;
        $this->system = $system;
        $this->inform = $inform;
        $this->display_option_name = "ナレッジ";
        $this->definition_value_approval = $this->repo->getVariableDefinitionApprovalWorkType($this->display_option_name);
    }

    public function getScreenData()
    {
        $status =  $this->repo->getStatus('ナレッジステータス');
        $category =  $this->repo->getCategory();
        $medical_subjects = $this->repo->getMedicalSubjects();
        $active_point = $this->repo->getActivePoint();
        $status_approval = $this->repo->getStatusApproval('承認ステータス');
        $parram_work_type = $this->repo->getStatus('承認業務区分');
        $approval_work_type  = 0;
        $key = array_search('ナレッジ', array_column($parram_work_type, 'definition_label'));
        if ($key !== false) {
            $approval_work_type  = $parram_work_type[$key]->definition_value ?? 0;
        }
        $parram_layer_num = $this->repo->getLayerNum();
        $approval_layer_num = $parram_layer_num->parameter_value ?? 0;

        return compact('status', 'category', 'medical_subjects', 'active_point', 'status_approval', 'approval_work_type', 'approval_layer_num');
    }

    public function getKnowledgeInput($knowledge_id, $user_cd, $approval_work_type, $approval_layer_num)
    {
        $is_knowledge_admin = false;
        $create_user_cd = null;
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');

        $status =  $this->repo->getStatusKnowledge($knowledge_id);
        if (is_array($status)) {
            $status = [
                'knowledge_status_type' => "10",
                'definition_label' => '作成中'
            ];
        }
        $request_type_param = $this->repo->getVariableDefinition('承認申請区分', 'ナレッジ');
        $request_type = $request_type_param->definition_value ?? 0;

        $knowledgeTab1AndTab3 = $this->repo->knowledgeTab1AndTab2($knowledge_id, $user_cd, $approval_layer_num, $request_type);
        $last_approver_layer = $this->repo->getLastApproverLayer($knowledge_id, $date_system, $approval_work_type, $approval_layer_num);
        $other_last_approver_layer = $this->repo->getOtherLastApproverLayer($knowledge_id, $date_system, $approval_work_type, $approval_layer_num);

        if (is_object($knowledgeTab1AndTab3)) {
            $knowledgeTab1AndTab3->users = !empty($knowledgeTab1AndTab3->users) ? json_decode($knowledgeTab1AndTab3->users) : [];
            $knowledgeTab1AndTab3->comment = !empty($knowledgeTab1AndTab3->comment) ? json_decode($knowledgeTab1AndTab3->comment) : [];
            $knowledgeTab1AndTab3->comment_orther_approval = !empty($knowledgeTab1AndTab3->comment_orther_approval) ? json_decode($knowledgeTab1AndTab3->comment_orther_approval) : [];
            $knowledgeTab1AndTab3->comment_last_approval = !empty($knowledgeTab1AndTab3->comment_last_approval) ? json_decode($knowledgeTab1AndTab3->comment_last_approval) : [];
            $knowledgeTab1AndTab3->active_point_cd = (string)($knowledgeTab1AndTab3->active_point_cd);
            $is_knowledge_admin = $knowledgeTab1AndTab3->check_knowledge_admin > 0 ? true : false;
            $create_user_cd = $knowledgeTab1AndTab3->create_user_cd ?? null;
        }

        if (is_array($knowledgeTab1AndTab3)) {
            $knowledgeTab1AndTab3 = (object)$knowledgeTab1AndTab3;
        }

        $note = $this->repo->knowledgeNotes();
        $knowledgeTimeLine = $this->repo->knowledgeTimeLine($knowledge_id);
        $time_line = [];
        if (count($knowledgeTimeLine)) {
            foreach ($knowledgeTimeLine as $key => $timelineItem) {
                $time_line[$key]['date'] = $timelineItem->start_datetime;
                $timelineItem = (object)$timelineItem;
                $timelineItem->timeline_details = !empty($timelineItem->timeline_details) ? json_decode($timelineItem->timeline_details) : [];
                if (count($timelineItem->timeline_details) > 0) {
                    $data = [];
                    $channel_type_10 = [];
                    foreach ($timelineItem->timeline_details as $timeline_details) {
                        if ($timeline_details->channel_type == 10) {
                            // 面談
                            $channel_type_10[] = $timeline_details;
                        } elseif ($timeline_details->channel_type == 20) {
                            // 説明会
                            $data[] = $this->repo->knowledgeTimeLine20($timeline_details->timeline_id, $timeline_details->channel_type, $knowledge_id);
                        } elseif ($timeline_details->channel_type == 30) {
                            // 講演会
                            $data[] = $this->repo->knowledgeTimeLine30($timeline_details->timeline_id, $timeline_details->channel_type, $knowledge_id);
                        } else {
                            // 外部コンテンツ ...
                            $data[] = $this->repo->knowledgeTimeLineNone($timeline_details->timeline_id, $timeline_details->channel_type, $knowledge_id);
                        }
                    }

                    // 面談
                    if (count((array)$channel_type_10) > 0) {
                        $timeline_id_10 = array_column($channel_type_10, 'timeline_id');
                        $time_line_10 = $this->repo->knowledgeTimeLine10($timeline_id_10, 10, $knowledge_id);
                        if (count($time_line_10) > 0) {
                            foreach ($time_line_10 as $item) {
                                $call = $this->repo->getDetailByCallID($item->call_id);
                                $item->channel_detail = $call;
                            }
                        }
                        $data = array_merge($data, $time_line_10);
                    }
                    foreach($data as $value){
                        if (date('H:i:s', strtotime($value->start_datetime)) == '00:00:00' && date('H:i:s', strtotime($value->end_datetime)) == '23:59:59') {
                            $value->all_day_flag = 1;
                        } else {
                            $value->all_day_flag = 0;
                        }
                    }

                    array_multisort(
                        array_column($data, 'all_day_flag'),
                        SORT_DESC,
                        array_column($data, 'start_datetime'),
                        SORT_DESC,
                        array_column($data, 'off_label_flag'),
                        SORT_DESC,
                        $data
                    );
                    $time_line[$key]['data'] = $data;
                }
            }
        }

        $data_approval['approval_request'] = $this->repo->getApprovalRequest($knowledge_id, $request_type);
        $data_approval['approval_request_detail'] = $this->repo->getApprovalLayer($knowledge_id, null);

        return compact('status', 'knowledgeTab1AndTab3', 'create_user_cd', 'last_approver_layer', 'other_last_approver_layer', 'is_knowledge_admin', 'note', 'time_line', 'data_approval');
    }

    public function UpdateAndCreate($knowledge_id, $request)
    {
        $data_master = [];
        $knowledge_status_type = $this->getKnowledgeStatus('ナレッジステータス', '作成中');
        $request->knowledge_status_type = $knowledge_status_type->definition_value ?? 0;
        $facility_group = $this->repo->getFacilityGroup($request->facility_cd);
        $request->facility_type_group_cd = $facility_group->facility_category_type ?? 0;
        $request->facility_type_group_name = $facility_group->facility_category_name ?? null;
        $request->create_datetime = $request->last_update_datetime  = date_create($this->system->getCurrentSystemDateTime());
        $request->create_user_cd = !empty($request->create_user_cd) ? $request->create_user_cd : $request->user_cd;
        // t_knowledge_timeline
        $time_line = $request->time_line ?? [];
        if (!empty($request->facility_cd ?? '') && !empty($request->person_cd ?? '')) {
            $data_master = $this->repo->getMasterFacilityPerson($request->facility_cd, $request->person_cd);
        }
        $request->department_cd = $data_master->department_cd ?? null;
        $request->department_name = $data_master->department_name ?? null;
        $request->display_position_cd = $data_master->display_position_cd ?? null;
        $request->display_position_name = $data_master->display_position_name ?? null;

        $t_knowledge = $this->repo->getKnowledge($knowledge_id);
        if (!empty($t_knowledge->knowledge_id ?? '') && $request->mode_screen == "edit") {
            $this->repo->updateKnowledgeInput($knowledge_id, $request);
        } else {
            $knowledge_data = $this->repo->createKnowledgeInput($request);
            $knowledge_id = $knowledge_data->knowledge_id;
        }

        $knowledge_id = !empty($t_knowledge->knowledge_id ?? '') ? $t_knowledge->knowledge_id : $knowledge_id;
        if (!empty($knowledge_id) && count($request->users) > 0) {
            $this->repo->deleteKnowledgeCollaborator($knowledge_id);
            $user_cds = array_unique(array_column($request->users, 'user_cd'));
            if (count($user_cds) > 0) {
                $datas = [];
                foreach ($user_cds as $item) {
                    $datas[] = [
                        'knowledge_id' => $knowledge_id,
                        'user_cd' => $item
                    ];
                }
                $this->repo->createKnowledgeCollaborator($datas);
            }
        }
        // t_knowledge_timeline
        $this->knowledgeTimeline($knowledge_id, $time_line);

        return ["knowledge_id" => $knowledge_id, "knowledge_status_type"  => $knowledge_status_type];
    }

    public function knowledgeTimeline($knowledge_id, $time_line)
    {
        if (count($time_line) > 0) {
            foreach ($time_line as $timeline_item) {
                $timeline_item = (object)$timeline_item;
                if (count($timeline_item->data) > 0) {
                    foreach ($timeline_item->data as $data_item) {
                        $data_item = (object)$data_item;
                        $data_item->timeline_id = explode(',', $data_item->timeline_id);
                        if (is_array($data_item->timeline_id) && count($data_item->timeline_id) > 0) {
                            foreach ($data_item->timeline_id as $timeline_item) {
                                $t_knowledge_timeline = $this->repo->checkKnowledgeTimeline($knowledge_id, $timeline_item);
                                if (count($t_knowledge_timeline) > 0 && $data_item->delete_flag == 0) {
                                    $this->repo->updateKnowledgeTimeline($knowledge_id, $timeline_item, $data_item->comment ?? '');
                                } elseif (count($t_knowledge_timeline) == 0 && $data_item->delete_flag == 0) {
                                    $this->repo->insertKnowledgeTimeline($knowledge_id, $data_item->channel_type, $timeline_item, $data_item->channel_id, $data_item->comment ?? '');
                                } elseif (count($t_knowledge_timeline) > 0 && $data_item->delete_flag == 1) {
                                    $this->repo->deleteKnowledgeTimelineDetails($knowledge_id, $timeline_item);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function nonePublic($user_cd, $knowledge_id, $approval_work_type, $approval_layer_num, $request)
    {
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        // get status
        $status_40 = 0;
        $status =  $this->repo->getStatus('ナレッジステータス');
        if (count($status) > 0) {
            $key_40 = array_search('公開中', array_column($status, 'definition_label'));
            if ($key_40 !== false) {
                $status_40 = $status[$key_40]->definition_value;
            }
        }

        // status knowledge current
        $knowledge_status_temp =  $this->repo->getStatusKnowledge($knowledge_id);
        $knowledge_status = $knowledge_status_temp->knowledge_status_type ?? 0;

        $last_approver_layer = $this->repo->getLastApproverLayer($knowledge_id, $date_system, $approval_work_type, $approval_layer_num);
        $data_role = $this->repo->checkRole($user_cd, 'R60');
        $admin_status = count($data_role) > 0 ? true : false;
        if (
            ($status_40 == $knowledge_status && in_array($user_cd, array_column($last_approver_layer, 'approval_user_cd'))) ||
            ($status_40 == $knowledge_status && $admin_status)
        ) {
            $knowledge_status_type = $this->getKnowledgeStatus('ナレッジステータス', '非公開');
            $request->knowledge_status_type = $knowledge_status_type = $knowledge_status_type->definition_value ?? '';
            $this->repo->nonePublic($knowledge_id, $knowledge_status_type);

            // update edit admin
            if ($request->mode_screen == "admin") {
                $facility_group = $this->repo->getFacilityGroup($request->facility_cd);
                $request->facility_type_group_cd = $facility_group->facility_category_type ?? 0;
                $request->facility_type_group_name = $facility_group->facility_category_name ?? null;
                // t_knowledge_timeline
                if (!empty($request->facility_cd ?? '') && !empty($request->person_cd ?? '')) {
                    $data_master = $this->repo->getMasterFacilityPerson($request->facility_cd, $request->person_cd);
                }
                $request->department_cd = $data_master->department_cd ?? null;
                $request->department_name = $data_master->department_name ?? null;
                $request->display_position_cd = $data_master->display_position_cd ?? null;
                $request->display_position_name = $data_master->display_position_name ?? null;
                $t_knowledge = $this->repo->getKnowledge($knowledge_id);
                $request->last_update_datetime = $t_knowledge->last_update_datetime;
                $this->editAdmin($request);
            }

            // update comment approval user
            $comment_none_public = $request->comment_none_public ?? [];
            $active_layer_num = $request->active_layer_num;
            if (count($comment_none_public) > 0 && $comment_none_public['approval_user_cd'] == $request->user_cd && $comment_none_public['layer_num'] == $active_layer_num) {
                $this->repo->updateCommentApprovalRequestDetail($request->request_id, $comment_none_public['layer_num'], $comment_none_public['approval_user_cd'], $comment_none_public['comment'] ?? '');
            }
            return true;
        }
        return false;
    }

    public function getKnowledgeStatus($definition_name, $definition_label)
    {
        $knowledge_status_type = 0;
        $status =  $this->repo->getStatus($definition_name);
        $key = array_search($definition_label, array_column($status, 'definition_label'));
        if ($key !== false) {
            $knowledge_status_type = $status[$key];
        }
        return $knowledge_status_type;
    }

    public function createInform($knowledge_id, $inform_contents, $create_user_cd, $inform_category_cd)
    {
        $not_received_inform_list = $this->inform->installed($create_user_cd);
        //Check if the user is set to receive notification type
        foreach ($not_received_inform_list as $value) {
            if ($value->inform_category_cd == $inform_category_cd && $value->checked == 0) {
                return true;
            }
        }
        $inform_id = $this->inform->registerInform($create_user_cd, $create_user_cd, $inform_contents, $inform_category_cd);
        return $this->inform->registerInformParameter($create_user_cd, $inform_id, 'knowledge_id', $knowledge_id);
    }



    public function deleteKnowledge($knowledge_id, $user_cd, $create_user_cd)
    {
        // check button role button delete
        $knowledge_status_type = $this->getKnowledgeStatus('ナレッジステータス', '作成中');
        $knowledge_status_type = $knowledge_status_type->definition_value ?? 0;
        // get status know
        $status =  $this->repo->getStatusKnowledge($knowledge_id);

        if (($status->knowledge_status_type ?? 0) != $knowledge_status_type || $user_cd != $create_user_cd) {
            return false;
        }
        $request_type_param = $this->repo->getVariableDefinition('承認申請区分', 'ナレッジ');
        $request_type = $request_type_param->definition_value ?? 0;
        $this->repo->deleteKnowledge($knowledge_id);
        $this->repo->deleteKnowledgeCollaborator($knowledge_id);
        $this->repo->deleteApprovalRequest($knowledge_id, $request_type);
        $this->repo->deleteKnowledgeTimeline($knowledge_id);
        return true;
    }

    public function approve($request)
    {
        $knowledge_id = $request->knowledge_id;
        $t_knowledge = $this->repo->getKnowledge($knowledge_id);

        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        $status_type_temp = $this->getKnowledgeStatus('ナレッジステータス', '承認確認中');
        $status_type_temp = $status_type_temp->definition_value ?? 0;

        $approval_work_type  = $request->approval_work_type;
        // check user approval
        $approval_datetime = $this->system->getCurrentSystemDateTime();
        $approval_layer_num = $request->approval_layer_num;
        $data_parram = $this->repo->getVariableDefinition('承認ステータス', '承認済み');
        $status_type = $data_parram->definition_value ?? 0;

        $other_last_approver_layer = $this->repo->getOtherLastApproverLayer($knowledge_id, $date_system, $approval_work_type, $approval_layer_num);
        $approval_user_cd_temp = array_column($other_last_approver_layer, 'approval_user_cd');
        // get status know current 
        $status =  $this->repo->getStatusKnowledge($knowledge_id);
        $know_status_current = $status->knowledge_status_type ?? 0;

        if ($status_type_temp != $know_status_current || !in_array($request->user_cd, $approval_user_cd_temp)) {
            return false;
        }

        if (!empty($t_knowledge->knowledge_id ?? '')) {
            // create inform
            $data_inform = $this->repo->getInformCategory('ナレッジ承認');
            $inform_category_cd = $data_inform->inform_category_cd ?? '';

            $knowledge_status_type = $this->getKnowledgeStatus('ナレッジステータス', '公開待ち');
            $knowledge_status_type = $knowledge_status_type->definition_value ?? '';
            // table t_approval_request_detail
            $data_approval_layers = $this->repo->getApprovalLayer($knowledge_id, null);
            // update t_knowledge
            if (empty($t_knowledge->first_approval_datetime)) {
                $first_approval_datetime = $this->system->getCurrentSystemDateTime();
                // update table t_knowledge
                $this->repo->updateFirstReleaseDatetime($knowledge_id, $first_approval_datetime);
            }

            $t_active_point_temp = $this->repo->getActivePointTemp('ナレッジ_ナレッジ承認');
            $t_active_point = $this->repo->checkActivePoint($knowledge_id);
            $active_point_parram = $this->repo->getVariableDefinition('活用度ポイント対象区分', 'ナレッジ');

            if (count($data_approval_layers) > 0) {
                $layer_temp1 = $approval_layer_num - 1;
                foreach ($data_approval_layers as $data_item) {
                    if ($request->user_cd == $data_item->approval_user_cd && $data_item->status_type != $status_type && $data_item->status_type == 0 && $data_item->layer_num == $request->active_layer_num) {
                        // update table t_approval_request_detail 
                        $data_approval_layers_temp = $this->repo->getApprovalLayer($knowledge_id, $data_item->layer_num);
                        $key_search = array_search($request->user_cd, array_column($data_approval_layers_temp, 'approval_user_cd'));
                        if ($key_search !== false) {
                            // update table t_approval_request_detail
                            $this->repo->updateApprovalRequestDetail($data_approval_layers_temp[$key_search]->request_id, $data_approval_layers_temp[$key_search]->layer_num, $data_approval_layers_temp[$key_search]->approval_user_cd, $status_type, $approval_datetime);
                        }

                        // send inform approval user with layer next
                        $layer_num_temp = $data_item->layer_num + 1;
                        $other_approver_layer = $this->repo->getApprovalLayer($knowledge_id, $layer_num_temp);

                        if (count($other_approver_layer) > 0) {
                            foreach ($other_approver_layer as $other_temp) {
                                $this->createInform($knowledge_id, $request->inform_contents, $other_temp->approval_user_cd, $inform_category_cd);
                                // update status t_approval_request_detail
                                $this->repo->updateApprovalRequestDetail($other_temp->request_id, $other_temp->layer_num, $other_temp->approval_user_cd, $data_item->status_type, null);
                            }
                        }

                        // update table t_approval_request
                        $this->repo->updateApprovalRequest($data_approval_layers_temp[$key_search]->request_id, $data_item->status_type, (int)$request->active_layer_num + 1);

                        if ($data_item->layer_num == $layer_temp1) {
                            // update table t_knowledge
                            $this->repo->updateStatusKnowledge($knowledge_id, $knowledge_status_type);
                            // send inform user create knowledge
                            // $this->createInform($knowledge_id, $request->inform_contents, $request->create_user_cd, $inform_category_cd);
                            $this->repo->updateApprovalRequest($data_approval_layers_temp[$key_search]->request_id, $data_item->status_type, (int)$request->active_layer_num + 1);
                        }

                        break;
                    }
                }

                // create active point
                if (count($t_active_point) == 0) {
                    $this->repo->createActivePoint($knowledge_id, $t_active_point_temp->active_point ?? 0, $active_point_parram->definition_value ?? null, $request->message, $request->create_user_cd);
                }
            }
            // update comment approval user
            $comment_orther_approval = $request->comment_orther_approval ?? [];
            $active_layer_num = $request->active_layer_num;
            if (count($comment_orther_approval) > 0 && $comment_orther_approval['approval_user_cd'] == $request->user_cd && $comment_orther_approval['layer_num'] == $active_layer_num) {
                $this->repo->updateCommentApprovalRequestDetail($request->request_id, $comment_orther_approval['layer_num'], $comment_orther_approval['approval_user_cd'], $comment_orther_approval['comment'] ?? '');
            }
        }
        return true;
    }

    public function remand($request)
    {
        $knowledge_id = $request->knowledge_id;
        $approval_datetime = $this->system->getCurrentSystemDateTime();
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        // get status
        $status_20 = $status_30 = $status_50 = 0;

        $status =  $this->repo->getStatus('ナレッジステータス');
        if (count($status) > 0) {
            $key_20 = array_search('承認確認中', array_column($status, 'definition_label'));
            if ($key_20 !== false) {
                $status_20 = $status[$key_20]->definition_value;
            }
            $key_30 = array_search('公開待ち', array_column($status, 'definition_label'));
            if ($key_30 !== false) {
                $status_30 = $status[$key_30]->definition_value;
            }
            $key_50 = array_search('不採用', array_column($status, 'definition_label'));
            if ($key_50 !== false) {
                $status_50 = $status[$key_50]->definition_value;
            }
        }

        // status knowledge current
        $knowledge_status_temp =  $this->repo->getStatusKnowledge($knowledge_id);
        $knowledge_status = $knowledge_status_temp->knowledge_status_type ?? 0;

        $other_last_approver_layer = $this->repo->getOtherLastApproverLayer($knowledge_id, $date_system, $request->approval_work_type, $request->approval_layer_num);
        $last_approver_layer = $this->repo->getLastApproverLayer($knowledge_id, $date_system, $request->approval_work_type, $request->approval_layer_num);
        $data_role = $this->repo->checkRole($request->user_cd, 'R60');
        $admin_status = count($data_role) > 0 ? true : false;

        if (
            ($status_20 == $knowledge_status && in_array($request->user_cd, array_column($other_last_approver_layer, 'approval_user_cd'))) ||
            ($status_30 == $knowledge_status && in_array($request->user_cd, array_column($last_approver_layer, 'approval_user_cd'))) ||
            ($status_50 == $knowledge_status && in_array($request->user_cd, array_column($last_approver_layer, 'approval_user_cd'))) ||
            ($status_50 == $knowledge_status && $admin_status)
        ) {
            $t_knowledge = $this->repo->getKnowledge($knowledge_id);
            if (!empty($t_knowledge->knowledge_id ?? '')) {
                $data_parram = $this->repo->getVariableDefinition('承認ステータス', '差戻');
                $status_type = $data_parram->definition_value ?? 0;
                $data_approval_layers = $this->repo->getApprovalLayer($knowledge_id, null);
                if (count($data_approval_layers) > 0) {
                    foreach ($data_approval_layers as $data_item) {
                        if ($request->user_cd == $data_item->approval_user_cd && $request->active_layer_num == $data_item->layer_num) {
                            // status remand
                            $knowledge_status_type = $this->getKnowledgeStatus('ナレッジステータス', '作成中');
                            $request->knowledge_status_type = $knowledge_status_type = $knowledge_status_type->definition_value ?? '';
                            // update status remand knowledge
                            $this->repo->updateStatusKnowledge($knowledge_id, $knowledge_status_type);
                            // update status user remand | table t_approval_request_detail
                            $this->repo->updateApprovalRequestDetail($data_item->request_id, $data_item->layer_num, $data_item->approval_user_cd, $status_type, $approval_datetime);
                            // update status table t_approval_request
                            $this->repo->updateApprovalRequest($data_item->request_id, $status_type, $data_item->layer_num);
                            // send inform create know
                            $data_inform = $this->repo->getInformCategory('ナレッジ差戻し');
                            $inform_category_cd = $data_inform->inform_category_cd ?? '';
                            // send inform user create
                            $this->createInform($knowledge_id, $request->inform_contents, $request->create_user_cd, $inform_category_cd);
                            break;
                        }
                    }
                }

                // update edit admin
                if ($request->mode_screen == "admin") {
                    $facility_group = $this->repo->getFacilityGroup($request->facility_cd);
                    $request->facility_type_group_cd = $facility_group->facility_category_type ?? 0;
                    $request->facility_type_group_name = $facility_group->facility_category_name ?? null;
                    // t_knowledge_timeline
                    $time_line = $request->time_line ?? [];
                    if (!empty($request->facility_cd ?? '') && !empty($request->person_cd ?? '')) {
                        $data_master = $this->repo->getMasterFacilityPerson($request->facility_cd, $request->person_cd);
                    }
                    $request->department_cd = $data_master->department_cd ?? null;
                    $request->department_name = $data_master->department_name ?? null;
                    $request->display_position_cd = $data_master->display_position_cd ?? null;
                    $request->display_position_name = $data_master->display_position_name ?? null;
                    $this->editAdmin($request);
                }

                // update comment approval user
                $comment_remand = $request->comment_remand ?? [];
                $active_layer_num = $request->active_layer_num;
                if (count($comment_remand) > 0 && $comment_remand['approval_user_cd'] == $request->user_cd && $comment_remand['layer_num'] == $active_layer_num) {
                    $this->repo->updateCommentApprovalRequestDetail($request->request_id, $comment_remand['layer_num'], $comment_remand['approval_user_cd'], $comment_remand['comment'] ?? '');
                }
                return true;
            }
        }

        return false;
    }

    public function publicKnowledge($request)
    {
        // get knowledge
        $knowledge_id = $request->knowledge_id;
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        $request->last_update_datetime = $this->system->getCurrentSystemDateTime();
        // get status
        $status_30 = $status_50 = $status_60 = 0;
        $status =  $this->repo->getStatus('ナレッジステータス');
        if (count($status) > 0) {
            $key_30 = array_search('公開待ち', array_column($status, 'definition_label'));
            if ($key_30 !== false) {
                $status_30 = $status[$key_30]->definition_value;
            }
            $key_50 = array_search('不採用', array_column($status, 'definition_label'));
            if ($key_50 !== false) {
                $status_50 = $status[$key_50]->definition_value;
            }
            $key_60 = array_search('非公開', array_column($status, 'definition_label'));
            if ($key_60 !== false) {
                $status_60 = $status[$key_60]->definition_value;
            }
        }

        // status knowledge current
        $knowledge_status_temp =  $this->repo->getStatusKnowledge($knowledge_id);
        $knowledge_status = $knowledge_status_temp->knowledge_status_type ?? 0;
        // $other_last_approver_layer = $this->repo->getOtherLastApproverLayer($knowledge_id, $date_system, $request->approval_work_type, $request->approval_layer_num);
        $last_approver_layer = $this->repo->getLastApproverLayer($knowledge_id, $date_system, $request->approval_work_type, $request->approval_layer_num);

        $data_role = $this->repo->checkRole($request->user_cd, 'R60');
        $admin_status = count($data_role) > 0 ? true : false;
        // status public
        $knowledge_status_type = $this->getKnowledgeStatus('ナレッジステータス', '公開中');
        $request->knowledge_status_type = $knowledge_status_type = $knowledge_status_type->definition_value ?? '';

        if (
            ($status_30 == $knowledge_status && in_array($request->user_cd, array_column($last_approver_layer, 'approval_user_cd'))) ||
            ($status_50 == $knowledge_status && in_array($request->user_cd, array_column($last_approver_layer, 'approval_user_cd'))) ||
            ($status_50 == $knowledge_status && $admin_status) ||
            ($status_60 == $knowledge_status && in_array($request->user_cd, array_column($last_approver_layer, 'approval_user_cd'))) ||
            ($status_60 == $knowledge_status && $admin_status)
        ) {
            $t_knowledge = $this->repo->getKnowledge($knowledge_id);
            if (!empty($t_knowledge->knowledge_id ?? '')) {
                // get layer approval all
                $data_approval_layers = $this->repo->getApprovalLayer($knowledge_id, null);
                if (count($data_approval_layers) > 0) {
                    foreach ($data_approval_layers as $data_item) {
                        if (($request->user_cd == $data_item->approval_user_cd && $request->active_layer_num == $data_item->layer_num)
                            || (($admin_status && $data_approval_layers[count($data_approval_layers) - 1]->layer_num == $request->active_layer_num && $request->user_cd == $data_item->approval_user_cd && $request->active_layer_num == $data_item->layer_num) && ($status_50 == $knowledge_status || $status_60 == $knowledge_status || $status_30 == $knowledge_status))
                        ) {
                            $data_parram = $this->repo->getVariableDefinition('活用度ポイント対象区分', 'ナレッジ');

                            $status_type_parram = $this->repo->getVariableDefinition('承認ステータス', '承認済み');
                            $status_type = $status_type_parram->definition_value ?? 0;
                            // update table t_knowledge
                            if (empty($t_knowledge->first_release_datetime)) {
                                $first_release_datetime = $this->system->getCurrentSystemDateTime();
                            } else {
                                $first_release_datetime = $t_knowledge->first_release_datetime;
                            }

                            $last_update_datetime = $release_datetime = $approval_datetime = $this->system->getCurrentSystemDateTime();
                            $this->repo->updateKnowledgePublic($knowledge_id, $knowledge_status_type, $first_release_datetime, $release_datetime, $last_update_datetime);
                            // update status user remand | table t_approval_request_detail
                            $this->repo->updateApprovalRequestDetail($data_item->request_id, $data_item->layer_num, $data_item->approval_user_cd, $status_type, $approval_datetime);
                            // update status table t_approval_request
                            $this->repo->updateApprovalRequest($data_item->request_id, $status_type, $request->active_layer_num);

                            // create active point
                            $t_active_point_temp = $this->repo->getActivePointTemp('ナレッジ_ナレッジ公開');
                            $t_active_point = $this->repo->checkActivePoint($knowledge_id);

                            if (count($t_active_point) == 0) {
                                $this->repo->createActivePoint($knowledge_id, $t_active_point_temp->active_point ?? 0, $data_parram->definition_value ?? null, $request->message, $request->create_user_cd);
                            }

                            // send inform 
                            $data_inform = $this->repo->getInformCategory('ナレッジ公開');
                            $inform_category_cd = $data_inform->inform_category_cd ?? '';

                            // send inform user create
                            $this->createInform($knowledge_id, $request->inform_contents, $request->create_user_cd, $inform_category_cd);
                            // send inform user layer

                            $approval_layers_temp = $this->repo->getApprovalLayer($knowledge_id, null);
                            foreach ($approval_layers_temp as $layers_temp) {
                                if ($layers_temp->approval_user_cd != $data_item->approval_user_cd && $request->create_user_cd != $layers_temp->approval_user_cd) {
                                    $this->createInform($knowledge_id, $request->inform_contents, $layers_temp->approval_user_cd, $inform_category_cd);
                                }
                            }
                            break;
                        }
                    }
                }

                // update edit admin
                if ($request->mode_screen == "admin") {
                    $facility_group = $this->repo->getFacilityGroup($request->facility_cd);
                    $request->facility_type_group_cd = $facility_group->facility_category_type ?? 0;
                    $request->facility_type_group_name = $facility_group->facility_category_name ?? null;
                    // t_knowledge_timeline
                    $time_line = $request->time_line ?? [];
                    if (!empty($request->facility_cd ?? '') && !empty($request->person_cd ?? '')) {
                        $data_master = $this->repo->getMasterFacilityPerson($request->facility_cd, $request->person_cd);
                    }
                    $request->department_cd = $data_master->department_cd ?? null;
                    $request->department_name = $data_master->department_name ?? null;
                    $request->display_position_cd = $data_master->display_position_cd ?? null;
                    $request->display_position_name = $data_master->display_position_name ?? null;
                    $this->editAdmin($request);
                }
                // update comment approval user
                $comment_last_approval = $request->comment_last_approval ?? [];
                if (count($comment_last_approval) > 0 && $request->user_cd == $comment_last_approval['approval_user_cd'] && $comment_last_approval['layer_num'] == $request->active_layer_num) {
                    $this->repo->updateCommentApprovalRequestDetail($request->request_id, $comment_last_approval['layer_num'], $comment_last_approval['approval_user_cd'], $comment_last_approval['comment'] ?? '');
                }
                return true;
            }
        }
        return false;
    }

    public function rejection($knowledge_id, $submit_comment, $create_user_cd, $inform_contents, $user_cd, $approval_work_type, $approval_layer_num, $request)
    {
        $status_30 = 0;
        $status =  $this->repo->getStatus('ナレッジステータス');
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        if (count($status) > 0) {
            $key_30 = array_search('公開待ち', array_column($status, 'definition_label'));
            if ($key_30 !== false) {
                $status_30 = $status[$key_30]->definition_value;
            }
        }
        // status knowledge current
        $knowledge_status_temp =  $this->repo->getStatusKnowledge($knowledge_id);
        $knowledge_status = $knowledge_status_temp->knowledge_status_type ?? 0;
        $last_approver_layer = $this->repo->getLastApproverLayer($knowledge_id, $date_system, $approval_work_type, $approval_layer_num);

        if (
            $status_30 == $knowledge_status && in_array($user_cd, array_column($last_approver_layer, 'approval_user_cd'))
        ) {
            // get knowledge
            $t_knowledge = $this->repo->getKnowledge($knowledge_id);
            if (!empty($t_knowledge->knowledge_id ?? '')) {
                // get layer approval all
                $data_approval_layers = $this->repo->getApprovalLayer($knowledge_id, null);
                if (count($data_approval_layers) > 0) {
                    foreach ($data_approval_layers as $data_item) {
                        if ($user_cd == $data_item->approval_user_cd  && $request->active_layer_num == $data_item->layer_num) {
                            // status rejection
                            $knowledge_status_type = $this->getKnowledgeStatus('ナレッジステータス', '不採用');
                            $request->knowledge_status_type = $knowledge_status_type = $knowledge_status_type->definition_value ?? '';

                            // update status t_knowledge
                            $this->repo->noneRejection($knowledge_id, $knowledge_status_type, $submit_comment);

                            // send inform 
                            $data_inform = $this->repo->getInformCategory('ナレッジ不採用');
                            $inform_category_cd = $data_inform->inform_category_cd ?? '';

                            // send inform user create
                            $this->createInform($knowledge_id, $inform_contents, $create_user_cd, $inform_category_cd);
                            // send inform user layer
                            $approval_layers_temp = $this->repo->getApprovalLayer($knowledge_id, null);
                            foreach ($approval_layers_temp as $layers_temp) {
                                if ($layers_temp->approval_user_cd != $data_item->approval_user_cd) {
                                    $this->createInform($knowledge_id, $inform_contents, $layers_temp->approval_user_cd, $inform_category_cd);
                                }
                            }
                            break;
                        }
                    }
                }
                // update edit admin
                if ($request->mode_screen == "admin") {
                    $facility_group = $this->repo->getFacilityGroup($request->facility_cd);
                    $request->facility_type_group_cd = $facility_group->facility_category_type ?? 0;
                    $request->facility_type_group_name = $facility_group->facility_category_name ?? null;

                    // t_knowledge_timeline
                    if (!empty($request->facility_cd ?? '') && !empty($request->person_cd ?? '')) {
                        $data_master = $this->repo->getMasterFacilityPerson($request->facility_cd, $request->person_cd);
                    }
                    $request->department_cd = $data_master->department_cd ?? null;
                    $request->department_name = $data_master->department_name ?? null;
                    $request->display_position_cd = $data_master->display_position_cd ?? null;
                    $request->display_position_name = $data_master->display_position_name ?? null;
                    $this->editAdmin($request);
                }

                // update comment approval user
                $comment_reject = $request->comment_reject;
                $active_layer_num = $request->active_layer_num;
                if ($comment_reject['approval_user_cd'] == $user_cd && $comment_reject['layer_num'] == $active_layer_num) {
                    $this->repo->updateCommentApprovalRequestDetail($request->request_id, $comment_reject['layer_num'], $comment_reject['approval_user_cd'], $comment_reject['comment'] ?? '');
                }
                return true;
            }
        }

        return false;
    }

    public function getUserApproval($user_cd){
        return $this->repo->getUserApproval($this->definition_value_approval->definition_value, $user_cd);
    }

    public function submit($request)
    {
        $knowledge_status_temp = $request->knowledge_status_temp;
        // get status know 
        $status =  $this->repo->getStatusKnowledge($request->knowledge_id);
        $know_status = $status->knowledge_status_type ?? 0;
        if (($request->mode_screen == "edit" && $request->user_cd != $request->create_user_cd) || $knowledge_status_temp != $know_status) {
            return false;
        }
        $knowledge_id = $request->knowledge_id;
        $request_type_param = $this->repo->getVariableDefinition('承認申請区分', 'ナレッジ');
        $request_type = $request_type_param->definition_value ?? 0;
        // pram
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        // number last layer aprroval
        $approval_layer_num = $request->approval_layer_num;
        // data_inform
        $data_inform = $this->repo->getInformCategory('ナレッジ元提出');
        $inform_category_cd = $data_inform->inform_category_cd ?? '';
        // number approval work type
        $approval_work_type  = $request->approval_work_type;

        // get approval user (h_approval_user)
        $other_approver_layer = $this->repo->getOtherLastApproverLayer($knowledge_id, $date_system, $approval_work_type, $approval_layer_num);
        $last_approver_layer = $this->repo->getLastApproverLayer($knowledge_id, $date_system, $approval_work_type, $approval_layer_num);

        // status type
        $data_parram = $this->repo->getVariableDefinition('承認ステータス', '承認待ち');
        $status_type = $data_parram->definition_value ?? 0;
        $request_datetime = $this->system->getCurrentSystemDateTime();

        // approval user all
        $approval_user_cd_temp = array_merge($other_approver_layer, $last_approver_layer);

        // get status 承認確認中
        $knowledge_status_type = $this->getKnowledgeStatus('ナレッジステータス', '承認確認中');
        $knowledge_status_type = $knowledge_status_type->definition_value ?? '';
        // update status table t_knowledge
        $this->repo->updateKnowledgeSubmit($knowledge_id, $knowledge_status_type, $request->active_point_cd);

        // delete table t_approval_request && t_approval_request_detail
        $this->repo->deleteApprovalRequest($knowledge_id, $request_type);

        // insert table t_approval_request
        $t_approval_request_temp = $this->repo->createApprovalRequest($knowledge_id, !empty($request->create_user_cd) ? $request->create_user_cd : $request->user_cd, $request_type, $status_type, $request_datetime);
        // insert table t_approval_request_detail

        if (count($approval_user_cd_temp) > 0 && !empty($t_approval_request_temp->request_id ?? '')) {
            foreach ($approval_user_cd_temp as $other_approver_layer_item) {
                $this->repo->createApprovalRequestDetail($t_approval_request_temp->request_id, $other_approver_layer_item->approval_layer_num, $other_approver_layer_item->approval_user_cd,  $other_approver_layer_item->approval_layer_num == 1 ? $status_type : 0, null);
            }
        }

        // send inform
        if (count($other_approver_layer) > 0) {
            foreach ($other_approver_layer as $data_item) {
                if ($data_item->approval_layer_num == 1) {
                    $this->createInform($knowledge_id, $request->inform_contents, $data_item->approval_user_cd, $inform_category_cd);
                }
            }
        }

        return true;
    }

    public function editAdmin($request)
    {
        $knowledge_id = $request->knowledge_id;
        $t_knowledge = $this->repo->getKnowledge($knowledge_id);
        if (!empty($t_knowledge->knowledge_id ?? '')) {
            $this->repo->updateKnowledgeInput($knowledge_id, $request);
            if (count($request->users) > 0) {
                $this->repo->deleteKnowledgeCollaborator($knowledge_id);
                $user_cds = array_unique(array_column($request->users, 'user_cd'));
                if (count($user_cds ?? []) > 0) {
                    $datas = [];
                    foreach ($user_cds as $item) {
                        $datas[] = [
                            'knowledge_id' => $knowledge_id,
                            'user_cd' => $item
                        ];
                    }
                    $this->repo->createKnowledgeCollaborator($datas);
                }
            }
        }
        // t_knowledge_timeline
        $this->knowledgeTimeline($knowledge_id, $request->time_line ?? []);
    }
}
