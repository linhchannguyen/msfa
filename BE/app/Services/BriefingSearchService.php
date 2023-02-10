<?php

namespace App\Services;

use App\Repositories\BriefingSearch\BriefingSearchRepositoryInterface;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Services\FileService;
use function GuzzleHttp\json_decode;
use App\Repositories\Material\MaterialRepositoryInterface;
use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;
use App\Services\ConventionSearchService;

use App\Repositories\AttendantManagement\AttendantManagementRepositoryInterface;
use App\Repositories\VariableDefinition\VariableDefinitionRepositoryInterface;
use App\Services\SystemParameterService;

class BriefingSearchService extends BaseRepository
{
    private $repo, $schedule, $briefing_plan, $briefing_result, $briefing_status_10, $definition_value_approval, $repo_convention, $system, $category_name_approval_final;
    private $category_name_submit, $inform_contents_submit_plan, $parameter_key_briefing, $approval_layer_num_1, $remand_briefing, $approval_briefing, $category_name_approval;
    private $inform_contents_approval_final, $inform_contents_approval, $remand_flag, $un_remand_flag, $convention_service, $repo_attend, $variable, $definition_label_briefing_plan, $definition_label_briefing_result;
    private $display_option_name;
    public function __construct(
        BriefingSearchRepositoryInterface $repo,
        ScheduleRepositoryInterface $schedule,
        ConventionSearchRepositoryInterface $repo_convention,
        SystemParameterService $system,
        MaterialRepositoryInterface $medical,
        ConventionSearchService $convention_service,
        AttendantManagementRepositoryInterface $repo_attend,
        VariableDefinitionRepositoryInterface $variable
    ) {
        $this->repo = $repo;
        $this->convention_service = $convention_service;
        $this->repo_attend = $repo_attend;
        $this->variable = $variable;
        $this->schedule = $schedule;
        $this->system = $system;
        $this->repo_convention = $repo_convention;
        $this->display_option_name = '説明会';
        $this->briefing_plan = 1;
        $this->briefing_result = 2;
        $this->briefing_status_10 = 10;
        $this->medical = $medical;
        $this->definition_value_approval = $this->repo->getVariableDefinitionApprovalWorkType($this->display_option_name);
        $this->category_name_submit = '説明会提出';
        $this->category_name_approval = '説明会承認';
        $this->category_name_approval_final = '説明会承認(最終)';
        $this->category_name_remand = '説明会差戻し';
        $this->inform_contents_submit_plan = "説明会の承認をお願いします。";
        $this->inform_contents_approval_final = "説明会が承認されました。";
        $this->inform_contents_approval = "説明会の承認をお願いします。";
        $this->inform_contents_remand = "説明会が差戻しされました。";
        $this->parameter_key_briefing = 'briefing_id';
        $this->approval_layer_num_1 = 1;
        $this->remand_briefing = 2;
        $this->approval_briefing = 1;
        $this->remand_flag = 1;
        $this->un_remand_flag = 0;
        $this->definition_label_briefing_plan = '説明会計画';
        $this->definition_label_briefing_result = '説明会結果';
        $this->definition_label_briefing_usage_type = '説明会';
    }

    //List All Facility
    public function getDataIndex()
    {
        $result['briefing_type'] = $this->repo->getBriefingSession();
        $status_type = $this->repo->getInformationSession();
        array_unshift($status_type, array("status_type" => "", "status_type_label" => "全て"));
        $result['status_type'] = $status_type;
        return $result;
    }

    //get List Data Briefing
    public function getListDataBriefing($request)
    {
        $request->approval_work_type = $this->definition_value_approval->definition_value;
        $result = $this->repo->getListDataBriefing($request);
        if (count($result['records']) > 0) {
            foreach ($result['records'] as $item) {
                $item->briefing_product = $item->briefing_product ? json_decode($item->briefing_product) : [];
            }
        }
        return $result;
    }

    //get Data Index Detail Briefing
    public function getDataIndexDetailBriefing()
    {
        $result['briefing_type'] = $this->repo->getBriefingSession();
        $result['status_type'] = $this->repo->getInformationSession();
        $bento_disposal = $this->repo->getBentoDisposalMethod();
        array_unshift($bento_disposal, array("bento_disposal_value" => "", "bento_disposal_label" => "選択する"));
        $result['bento_disposal'] = $bento_disposal;
        $result['briefing_attendee_count'] = $this->repo->getBriefingAttendeeCount(null);
        $result['briefing_expense_item'] = $this->repo->getBriefingExpenseItem(null);
        $result['medical_subjects_group'] = $this->medical->allMedicalSubjects();
        $result['medical_staff'] = $this->repo_attend->getMedicalStaff();
        $result['request_type_plan'] = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_briefing_plan)->definition_value;
        $result['request_type_result'] = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_briefing_result)->definition_value;
        $result['document_usage_type'] = $this->repo_convention->getDocumentUsageType($this->definition_label_briefing_usage_type);
        return $result;
    }

    //get Data Detail Briefing
    public function getDataDetailBriefing($request)
    {
        $result = [];
        $briefing_detail = (object)$this->repo->getDataDetailBriefingByBriefingID($request->briefing_id, $request->schedule_id);
        $input_deadline_convention = $this->repo_convention->getInputDeadlineConvention($this->display_option_name);
        $date_now = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        if (isset($input_deadline_convention->parameter_value)) {
            if ($input_deadline_convention->parameter_value && isset($briefing_detail->schedule_id)) {
                $end_date = date("Y-m-d", strtotime("+" . $input_deadline_convention->parameter_value . " months", strtotime($briefing_detail->start_date)));
            } else {
                $end_date = date("Y-m-d", strtotime($briefing_detail->start_date));
            }
        } else {
            $end_date = "";
        }
        $briefing_detail->end_date = $end_date;
        $briefing_detail->briefing_attendee_count = $this->repo->getBriefingAttendeeCount($briefing_detail->briefing_id ?? null);
        $briefing_detail->briefing_expense_item = $this->repo->getBriefingExpenseItem($briefing_detail->briefing_id ?? null);
        if (isset($briefing_detail->schedule_id)) {
            $briefing_detail->briefing_product = $briefing_detail->briefing_product ? json_decode($briefing_detail->briefing_product) : [];
            $briefing_detail->briefing_document = $briefing_detail->briefing_document ? json_decode($briefing_detail->briefing_document) : [];
            $briefing_detail->briefing_attendee = $briefing_detail->briefing_attendee ? json_decode($briefing_detail->briefing_attendee) : [];
            $definition_label_briefing = $briefing_detail->status_type > 30 ? $this->definition_label_briefing_result : $this->definition_label_briefing_plan;
            $request_type = $this->variable->getVariableStatusTypeApprovalRequest($definition_label_briefing)->definition_value;
            $user_approval = $this->repo->getUserApprovalBriefing($request->user_login, $briefing_detail->briefing_id, $request_type, null);
            $result['user_approval'] = count($user_approval) > 0 ? 1 : 0;
            $result['layer_user_approval'] = $user_approval;
            $active_layer_approval = $this->repo_convention->getApprovalRequest($briefing_detail->briefing_id, $request_type);
            $result['active_layer_approval'] = isset($active_layer_approval->request_id) ? $active_layer_approval->active_layer_num : 1;
        }
        $result['status_report_approval'] = isset($briefing_detail->schedule_id) ? $this->convention_service->checkStatusReport($briefing_detail->schedule_id, $briefing_detail->start_date) : false;
        //get detail briefing plan
        $result['briefing_plan'] = $this->repo->getUserApprovalBriefing(null, $briefing_detail->briefing_id ?? null, $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_briefing_plan)->definition_value, null);
        //get detail briefing result
        $result['briefing_result'] = $this->repo->getUserApprovalBriefing(null, $briefing_detail->briefing_id ?? null, $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_briefing_result)->definition_value, null);
        $result['briefing_detail'] = $briefing_detail;
        return $result;
    }

    //save Detail Briefing
    public function saveDetailBriefing($request)
    {
        $result = [];
        if (!$request->briefing_id) {
            $create_schedule = $this->createdSchedule($request);
            $request->schedule_id = $create_schedule->schedule_id;
            $briefing_detail = $this->repo->saveBriefing($request);
            $request->briefing_id_new = $briefing_detail->briefing_id;
            $result['briefing_id'] = $briefing_detail->briefing_id;
        } else {
            $this->updateSchedule($request);
            $this->repo->updateBriefing($request);
            $result['briefing_id'] = $request->briefing_id;
        }
        $this->saveBriefingProduct($request);
        $this->saveBriefingDocument($request);
        $this->saveBriefingPlanAttendeeCount($request);
        $this->saveBriefingExpenseItem($request);
        $this->saveBriefingAttendee($request);
        if ($request->request_type == $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_briefing_result)->definition_value) {
            $this->addDocumentUsageSituation($request);
        }
        if ($request->status_type == 40 && !$request->briefing_id) {
            $expense_item = $this->repo->getBriefingExpenseItem($result['briefing_id']);
            $attendee_count = $this->repo->getBriefingAttendeeCount($result['briefing_id']);
            foreach ($attendee_count as $value) {
                $this->repo->updateBriefingResultAttendeeCount($result['briefing_id'], $value->occupation_type, $value->result_headcount ?? null);
            }
            foreach ($expense_item as $value) {
                $value->plan_unit_price = $value->result_unit_price ?? null;
                $value->plan_quantity = $value->result_quantity ?? null;
                $value->plan_amount = $value->result_amount ?? null;
                $this->repo->updateBriefingResultExpenseItem($result['briefing_id'], $value);
            }
        }
        return $result;
    }

    //save Briefing Product
    public function saveBriefingProduct($request)
    {
        if (!$request->briefing_id) {
            if (count($request->briefing_product) > 0) {
                foreach ($request->briefing_product as $item_briefing_product) {
                    $item_briefing_product = (object)$item_briefing_product;
                    $this->repo->saveBriefingProduct($request->briefing_id_new, $item_briefing_product->product_cd, $item_briefing_product->product_name);
                }
            }
        } else {
            if (count($request->briefing_product) > 0) {
                foreach ($request->briefing_product as $item_briefing_product) {
                    $item_briefing_product = (object)$item_briefing_product;
                    if ($item_briefing_product->delete_flag == 1) {
                        $this->repo->saveBriefingProduct($request->briefing_id, $item_briefing_product->product_cd, $item_briefing_product->product_name);
                    } elseif ($item_briefing_product->delete_flag == -1) {
                        $this->repo->deleteBriefingProduct($request->briefing_id, $item_briefing_product->product_cd);
                    }
                }
            }
        }
        return true;
    }

    //save Briefing Document
    public function saveBriefingDocument($request)
    {
        if (!$request->briefing_id) {
            if (count($request->briefing_document) > 0) {
                foreach ($request->briefing_document as $item_briefing_document) {
                    $item_briefing_document = (object)$item_briefing_document;
                    $this->repo->saveBriefingDocument($request->briefing_id_new, $item_briefing_document->document_id);
                }
            }
        } else {
            if (count($request->briefing_document) > 0) {
                foreach ($request->briefing_document as $item_briefing_document) {
                    $item_briefing_document = (object)$item_briefing_document;
                    if ($item_briefing_document->delete_flag == 1) {
                        $this->repo->saveBriefingDocument($request->briefing_id, $item_briefing_document->document_id);
                    } elseif ($item_briefing_document->delete_flag == -1) {
                        $this->repo->deleteBriefingDocument($request->briefing_id, $item_briefing_document->document_id);
                    }
                }
            }
        }
        return true;
    }

    //save Briefing Plan Attendee Count
    public function saveBriefingPlanAttendeeCount($request)
    {
        if (!$request->briefing_id) {
            if (count($request->briefing_attendee_count) > 0) {
                foreach ($request->briefing_attendee_count as $item_briefing_attendee_count) {
                    $item_briefing_attendee_count = (object)$item_briefing_attendee_count;
                    $this->repo->saveBriefingPlanAttendeeCount($request->briefing_id_new, $item_briefing_attendee_count->occupation_type, $item_briefing_attendee_count->plan_headcount, $item_briefing_attendee_count->result_headcount);
                }
            }
        } else {
            if (count($request->briefing_attendee_count) > 0) {
                foreach ($request->briefing_attendee_count as $item_briefing_attendee_count) {
                    $item_briefing_attendee_count = (object)$item_briefing_attendee_count;
                    $this->repo->updateBriefingPlanAttendeeCount($request->briefing_id, $item_briefing_attendee_count->occupation_type, $item_briefing_attendee_count->plan_headcount, $item_briefing_attendee_count->result_headcount);
                }
            }
        }
        return true;
    }

    //save Briefing Expense Item
    public function saveBriefingExpenseItem($request)
    {
        if (!$request->briefing_id) {
            if (count($request->briefing_expense_item) > 0) {
                foreach ($request->briefing_expense_item as $item_briefing_expense_item) {
                    $item_briefing_expense_item = (object)$item_briefing_expense_item;
                    $this->repo->saveBriefingExpenseItem($request->briefing_id_new, $item_briefing_expense_item);
                }
            }
        } else {
            if (count($request->briefing_expense_item) > 0) {
                foreach ($request->briefing_expense_item as $item_briefing_expense_item) {
                    $item_briefing_expense_item = (object)$item_briefing_expense_item;
                    $this->repo->updateBriefingExpenseItem($request->briefing_id, $item_briefing_expense_item);
                }
            }
        }
        return true;
    }

    //save Briefing Attendee
    public function saveBriefingAttendee($request)
    {
        if (!$request->briefing_id) {
            if (count($request->briefing_attendee) > 0) {
                foreach ($request->briefing_attendee as $item_briefing_attendee) {
                    $item_briefing_attendee = (object)$item_briefing_attendee;
                    $this->repo->saveBriefingAttendee($request->briefing_id_new, $item_briefing_attendee);
                }
            }
        } else {
            if (count($request->briefing_attendee) > 0) {
                foreach ($request->briefing_attendee as $item_briefing_attendee) {
                    $item_briefing_attendee = (object)$item_briefing_attendee;
                    if ($item_briefing_attendee->delete_flag == 1) {
                        $this->repo->saveBriefingAttendee($request->briefing_id, $item_briefing_attendee);
                    } elseif ($item_briefing_attendee->delete_flag == -1) {
                        $this->repo->deleteBriefingAttendee($item_briefing_attendee->briefing_attendee_id);
                    } elseif ($item_briefing_attendee->delete_flag == 0) {
                        $this->repo->updateBriefingAttendee($item_briefing_attendee);
                    }
                }
            }
        }
        return true;
    }

    //update schedule
    public function updateSchedule($request)
    {
        $schedule['start_date'] = $request->start_date;
        $schedule['start_time'] = $request->start_time;
        $schedule['end_time'] = $request->end_time;
        $schedule['all_day_flag'] = 0;
        $schedule['title'] = $request->briefing_name;
        $schedule['schedule_id'] = $request->schedule_id;
        return $this->schedule->updateScheduleConvention($schedule);
    }

    //created schedule
    public function createdSchedule($request)
    {
        $option_type = $this->repo->getDisplayOptionTpyeBriefing();
        $variable_schedule_type = $this->repo->getVariableDefinitionBriefing();
        $schedule['schedule_type'] = $variable_schedule_type->definition_value;
        $schedule['start_date'] = $request->start_date;
        $schedule['start_time'] = $request->start_time;
        $schedule['end_time'] = $request->end_time;
        $schedule['title'] = $request->briefing_name;
        $schedule['all_day_flag'] = 0;
        $schedule['display_option_type'] = $option_type->display_option_type;
        $schedule['user_cd'] = $request->user_login;
        $this->schedule->addSchedule($schedule);
        return $this->_lastInserted('t_schedule', 'schedule_id');
    }

    //get Data Detail Briefing
    public function updateStatusTypeBriefing($request)
    {
        return $this->repo->updateStatusTypeBriefing($request->briefing_id, $request->status_type);
    }

    //get Data Detail Briefing
    public function deleteDetailBriefing($request)
    {
        return $this->repo->deleteDetailBriefing($request->briefing_id);
    }

    public function getUserApproval($user_cd)
    {
        return $this->repo->getUserApproval($this->definition_value_approval->definition_value, $user_cd);
    }

    //submit plan Briefing
    public function submitBriefing($request)
    {
        $briefing_id = $request->briefing_id;
        $briefing_detail = $this->saveDetailBriefing($request);
        $request->briefing_id = $request->briefing_id ? $request->briefing_id : $briefing_detail['briefing_id'];
        $result['briefing_id'] = $request->briefing_id;
        // $request->briefing_detail = (object)$this->repo->getDataDetailBriefingByBriefingID($request->briefing_id, null);
        $approval_request = $this->repo->getRequestIDApproval($request->briefing_id, $request->request_type);
        if (isset($approval_request->request_id)) {
            $this->repo->deleteApprovalRequest($approval_request->request_id);
        }
        //create aproval request briefing
        $approval['request_type'] = $request->request_type;
        $approval['request_target_id'] = $request->briefing_id;
        $approval['request_datetime'] = $this->system->getCurrentSystemDateTime();
        $approval['request_user_cd'] = $request->user_login;
        $approval['status_type'] = 0;
        $approval['active_layer_num'] = 1;
        $approval_request_briefing = $this->repo_convention->addApprovalRequest($approval);
        $this->repo->updateStatusTypeBriefing($request->briefing_id, $request->status_type);
        $user_approval_briefing_plan = $this->repo->getUserApproval($this->definition_value_approval->definition_value, $request->user_login);
        if (count($user_approval_briefing_plan) > 0) {
            foreach ($user_approval_briefing_plan as $item_user_approval) {
                $approval_detail['request_id'] = $approval_request_briefing->request_id;
                $approval_detail['layer_num'] = $item_user_approval->approval_layer_num;
                $approval_detail['approval_user_cd'] = $item_user_approval->approval_user_cd;
                $approval_detail['status_type'] = 0;
                $approval_detail['approval_datetime'] = $this->system->getCurrentSystemDateTime();
                $approval_detail['comment'] = "";
                $this->repo_convention->addApprovalRequestDetail($approval_detail);
            }
        }
        $user_approval_briefing_plan_layer_1 = $this->repo->getUserApprovalBriefing(null, $request->briefing_id, $request->request_type, 1);
        if (count($user_approval_briefing_plan_layer_1) > 0) {
            $inform_category = $this->repo_convention->getInformCategory($this->category_name_submit);
            // add approval detail by user approval
            foreach ($user_approval_briefing_plan_layer_1 as $item_user_approval_layer_1) {
                $this->addInformBriefing($item_user_approval_layer_1->approval_user_cd, $inform_category->inform_category_cd, $request->briefing_id, $request->briefing_name, $this->inform_contents_submit_plan);
            }
        }
        if ($request->request_type == $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_briefing_result)->definition_value) {
            $this->addDocumentUsageSituation($request);
        }
        //update reamnd briefing
        $this->repo->updateRemandFlagBriefing($request->briefing_id, $this->un_remand_flag);
        if ($request->status_type == 50 && !$briefing_id) {
            $expense_item = $this->repo->getBriefingExpenseItem($request->briefing_id);
            $attendee_count = $this->repo->getBriefingAttendeeCount($request->briefing_id);
            foreach ($attendee_count as $value) {
                $this->repo->updateBriefingResultAttendeeCount($request->briefing_id, $value->occupation_type, $value->result_headcount ?? null);
            }
            foreach ($expense_item as $value) {
                $value->plan_unit_price = $value->result_unit_price ?? null;
                $value->plan_quantity = $value->result_quantity ?? null;
                $value->plan_amount = $value->result_amount ?? null;
                $this->repo->updateBriefingResultExpenseItem($request->briefing_id, $value);
            }
        }
        return $result;
    }

    //submit plan Briefing
    public function approvalBriefing($request)
    {
        //Check user approval finnal
        $user_approval = $this->repo->checkUserApprovalFinal($request->user_login, $request->briefing_id, $request->request_type);
        $inform_contents = $this->inform_contents_approval;
        $category_name_approval = $this->category_name_approval;
        $approval_request = $this->repo->getRequestIDApproval($request->briefing_id, $request->request_type);
        $layer_num_approval_finnal = $this->repo->getLayerNumApprovalFinnalBriefing();
        $inform_category = $this->repo_convention->getInformCategory($category_name_approval);
        $expense_item = $this->repo->getBriefingExpenseItem($request->briefing_id);
        $attendee_count = $this->repo->getBriefingAttendeeCount($request->briefing_id);
        if ($request->active_layer_approval == $layer_num_approval_finnal->parameter_value && is_object($user_approval)) {
            //aprroval briefing finnal
            $this->repo->updateApprovalBriefingFinal($request->briefing_id, $this->approval_briefing, $request->request_type);
            //update status briefing
            $this->repo->updateStatusTypeBriefing($request->briefing_id, $request->status_type);
            $inform_contents = $this->inform_contents_approval_final;
            $category_name_approval = $this->category_name_approval_final;
            $inform_category = $this->repo_convention->getInformCategory($category_name_approval);
            $this->repo->updateApprovalBriefingDetail($approval_request->request_id, $request->user_login, $this->approval_briefing, $request->comment, $request->active_layer_approval);
            foreach ($attendee_count as $value) {
                $this->repo->updateBriefingResultAttendeeCount($request->briefing_id, $value->occupation_type, ($request->status_type == 60) ? $value->result_headcount : $value->plan_headcount ?? null);
            }
            foreach ($expense_item as $value) {
                if ($request->status_type == 60) {
                    $value->plan_unit_price = $value->result_unit_price;
                    $value->plan_quantity = $value->result_quantity;
                    $value->plan_amount = $value->result_amount;
                }
                $this->repo->updateBriefingResultExpenseItem($request->briefing_id, $value);
            }
            $this->addInformBriefing($request->briefing_detail->implement_user_cd, $inform_category->inform_category_cd, $request->briefing_id, $request->briefing_name, $inform_contents);
            return true;
        } else {
            $this->repo_convention->updateActiveApprovalRequest($approval_request->request_id, $request->active_layer_approval + 1);
            $this->repo->updateApprovalBriefingDetail($approval_request->request_id, $request->user_login, $this->approval_briefing, $request->comment, $request->active_layer_approval);
            $user_approval_briefing_plan = $this->repo->getUserApprovalBriefing(null, $request->briefing_id, $request->request_type, $request->active_layer_approval + 1);
            if (count($user_approval_briefing_plan) > 0) {
                //add inform
                foreach ($user_approval_briefing_plan as $item) {
                    $this->addInformBriefing($item->approval_user_cd, $inform_category->inform_category_cd, $request->briefing_id, $request->briefing_name, $inform_contents);
                }
            }
        }

        return true;
    }

    //submit plan Briefing
    public function remandBriefing($request)
    {
        $inform_category = $this->repo_convention->getInformCategory($this->category_name_remand);
        //aprroval briefing finnal
        $this->repo->updateApprovalBriefingFinal($request->briefing_id, $this->remand_briefing, $request->request_type);
        //update status briefing
        $this->repo->updateStatusTypeBriefing($request->briefing_id, $request->status_type);
        //update reamnd briefing
        $this->repo->updateRemandFlagBriefing($request->briefing_id, $this->remand_flag);
        $approval_request = $this->repo->getRequestIDApproval($request->briefing_id, $request->request_type);
        $this->repo->updateApprovalBriefingDetail($approval_request->request_id, $request->user_login, $this->remand_briefing, $request->comment, $request->active_layer_approval);
        $this->addInformBriefing($request->briefing_detail->created_by, $inform_category->inform_category_cd, $request->briefing_id, $request->briefing_name, $this->inform_contents_remand);
        return true;
    }

    //add Inform
    public function addInformBriefing($inform_user_cd, $inform_category_cd, $briefing_id, $briefing_name, $inform_contents)
    {
        $not_received_inform_list = $this->repo_convention->installed($inform_user_cd, $inform_category_cd);
        //Check if the user is set to receive notification type
        $check_not_received = 0;
        foreach ($not_received_inform_list as $value) {
            if ($value->checked == 0) {
                $check_not_received++;
            }
        }
        if ($check_not_received == 0) {
            $infrom['inform_category_cd'] = $inform_category_cd;
            $infrom['inform_datetime'] = $this->system->getCurrentSystemDateTime();
            $infrom['inform_contents'] = $briefing_name . $inform_contents;
            $infrom['inform_user_cd'] = $inform_user_cd;
            $infrom_id = $this->repo_convention->addInform($infrom);
            $inform_parameter['parameter_key'] = $this->parameter_key_briefing;
            $inform_parameter['parameter_value'] = $briefing_id;
            $inform_parameter['inform_id'] = $infrom_id;
            $this->repo_convention->addInformParameter($inform_parameter);
        }
    }

    //add Inform
    public function pendingBriefing($request)
    {
        $this->repo->updateRemandFlagBriefing($request->briefing_id, $this->un_remand_flag);
        $this->repo_convention->updateInvisibleFlagSchedule($request->briefing_detail->schedule_id ?? null);
        return $this->repo->updateStatusTypeBriefing($request->briefing_id, $request->status_type);
    }

    //add Document Usage Situation
    public function addDocumentUsageSituation($request)
    {
        if (isset($request->briefing_document)) {
            if (count($request->briefing_document) > 0) {
                $document_usage_type = $this->repo_convention->getDocumentUsageType($this->definition_label_briefing_usage_type);
                foreach ($request->briefing_document as $item) {
                    $item_briefing_document = (object)$item;
                    if ($item_briefing_document->delete_flag == -1) {
                        $this->repo_convention->deleteDocumentUsageSituation($request->briefing_id, $document_usage_type, $item_briefing_document->document_id);
                    } else {
                        $this->repo_convention->createDocumentUsageSituation($request, $request->briefing_id, $document_usage_type, $item_briefing_document->document_id);
                    }
                }
            }
        }
    }
}
