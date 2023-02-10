<?php

namespace App\Services;

use App\Repositories\ConventionSearch\ConventionSearchRepositoryInterface;
use App\Repositories\Schedule\ScheduleRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Repositories\DailyReport\DailyReportRepositoryInterface;
use App\Services\SystemParameterService;
use App\Services\FileService;
use function GuzzleHttp\json_decode;
use App\Repositories\AttendantManagement\AttendantManagementRepositoryInterface;
use Carbon\Carbon;
use App\Traits\StatusReportTrait;
use App\Repositories\DailyReport\DailyReportRepository;
use App\Repositories\VariableDefinition\VariableDefinitionRepositoryInterface;
use App\Repositories\BriefingSearch\BriefingSearchRepositoryInterface;
use JWTAuth;
use App\Traits\Base64StringFileTrait;
use stdClass;

class ConventionSearchService extends BaseRepository
{
    use StatusReportTrait;
    use Base64StringFileTrait;
    private $repo, $convention_plan, $convention_result, $fileService, $plan_approval_work_type, $system, $approval_layer_1, $inform_contents_submit_plan, $category_name_submit_plan;
    private $status_submit_plan, $status_submit_result, $parameter_key_convention, $remand_convention, $approval_convention, $un_remand_flag, $save_result_convention;
    private $variable, $definition_label_convention_plan, $definition_label_convention_result, $briefing, $inform_contents_approval, $inform_contents_approval_final, $inform_contents_remand;
    private $inform_contents_pending, $category_name_pending_convention, $approval_layer_2, $definition_label_report, $display_option_name;
    public function __construct(
        ConventionSearchRepositoryInterface $repo,
        ScheduleRepositoryInterface $schedule,
        SystemParameterService $system,
        FileService $fileService,
        AttendantManagementRepositoryInterface $attend,
        VariableDefinitionRepositoryInterface $variable,
        BriefingSearchRepositoryInterface $briefing
    ) {
        $this->repo = $repo;
        $this->briefing = $briefing;
        $this->attend = $attend;
        $this->variable = $variable;
        $this->schedule = $schedule;
        $this->convention_plan = 3;
        $this->convention_result = 4;
        $this->display_option_name = "講演会";
        $this->plan_approval_work_type = $this->briefing->getVariableDefinitionApprovalWorkType($this->display_option_name)->definition_value ?? null;
        $this->remand_flag = 1;
        $this->un_remand_flag = 0;
        $this->system = $system;
        $this->fileService = $fileService;
        $this->approval_layer_1 = 1;
        $this->approval_layer_2 = 2;
        $this->inform_contents_submit_plan = "の講演会の承認をお願いします";
        $this->inform_contents_remand = "の講演会が差戻しされました。";
        $this->inform_contents_approval = "の講演会の承認をお願いします。";
        $this->inform_contents_approval_final = "の講演会が承認されました。";
        $this->inform_contents_pending = "の講演会が中止されました。";
        $this->category_name_submit_plan = "講演会提出";
        $this->category_name_pending_convention = "講演会中止";
        $this->category_name_remand = "講演会差戻し";
        $this->category_name_approval = "講演会承認";
        $this->status_submit_plan = 30;
        $this->save_result_convention = 40;
        $this->status_submit_result = 60;
        $this->parameter_key_convention = "convention_id";
        $this->remand_convention = 2;
        $this->approval_convention = 1;
        $this->definition_label_convention_plan = "講演会計画";
        $this->definition_label_convention_result = "講演会結果";
        $this->definition_label_convention_usage_type = "講演会";
        $this->definition_label_report = "報告";
    }

    //List All Facility
    public function getData()
    {
        $status_type = $this->repo->getStatusType();
        array_unshift($status_type, array("status_type" => "", "status_type_label" => "全て"));
        $result['status_type'] = $status_type;
        $result['convention_type'] = $this->repo->getConventionType();
        return $result;
    }

    //List All Facility
    public function allData($request)
    {
        $request->approval_work_type = $this->plan_approval_work_type;
        return $this->repo->allData($request);
    }

    //List data index
    public function getIndex()
    {
        $user_cd = $this->getCurrentUser();
        $user_login = $this->getInfoCurrentUser($user_cd, IS_USER);
        $org_cd_login = $user_login->org_cd;
        $result['status_type'] = $this->repo->getStatusType();
        $result['convention_type'] = $this->repo->getConventionType();
        $all_select_org_code = $this->repo->getAllSelectOrgCode();
        $org_select_national = $all_select_org_code->parameter_value ?? "";
        $org_select_national = explode(',', $org_select_national);
        $select_national_flag = false;
        foreach ($org_select_national as $org_cd) {
            if ($org_cd == $org_cd_login) {
                $select_national_flag = true;
                break;
            }
        }
        $result['select_national_flag'] = $select_national_flag;
        $result['hold_type'] = $this->repo->getHoldType();
        $result['hold_method'] = $this->repo->getHoldMethod();
        $result['organization_layer'] = $this->repo->getOrganizationLayer();
        $result['hold_form'] = $this->repo->getHoldForm();
        $result['cost_share_type'] = $this->repo->getCostShareType();
        $result['expense_classification'] = $this->repo->getExpenseClassification();
        $result['series_registration'] = $this->repo->getSeriesRegistration();
        $result['bento_disposal'] = $this->repo->getBentoDisposal();
        $convention_status_item = $this->repo->getConventionStatusItem();
        array_push($convention_status_item, array("status_item_cd" => "", "status_item_name" => "フォロー"));
        $result['convention_status_item'] = $convention_status_item;
        $convention_occupation_type = $this->repo->getConventionOccupationType();
        array_push($convention_occupation_type, array("occupation_type" => "", "occupation_name" => "合計"));
        $result['convention_occupation_type'] = $convention_occupation_type;
        $result['document_usage_type'] = $this->repo->getDocumentUsageType($this->definition_label_convention_usage_type);
        $area_expense = $this->repo->allConventionExpenseItem();
        $area_expense_sum_plan = 0;
        $area_expense_sum_result = 0;
        if (count($area_expense) > 0) {
            foreach ($area_expense as $item_layer) {
                $item_layer->plan_amount = 0;
                $item_layer->result_amount = 0;
                $layer_2 = $this->repo->getConventionAreaExpenseLayer($item_layer->expense_item_cd, 0);
                if (count($layer_2) > 0) {
                    foreach ($layer_2 as $item) {
                        if ((int)$item->layer_num < 3) {
                            $layer_3 = $this->repo->getConventionAreaExpenseLayer($item->expense_item_cd, 0);
                            foreach ($layer_3 as $item_layer_3) {
                                $item_layer_3->plan_amount = $item_layer_3->plan_amount ? (int)$item_layer_3->plan_amount : null;
                                $item_layer_3->result_amount = $item_layer_3->result_amount ? (int)$item_layer_3->result_amount : null;
                            }
                            $item->layer_3 = $layer_3;
                        }
                        $item->plan_amount = $item->plan_amount ? (int)$item->plan_amount : null;
                        $item->result_amount = $item->result_amount ? (int)$item->result_amount : null;
                    }
                }
                $item_layer->layer_2 = $layer_2;
                $area_expense_sum_plan += $item_layer->plan_amount;
                $area_expense_sum_result += $item_layer->result_amount;
                $item_layer->plan_amount = (int)$item_layer->plan_amount;
                $item_layer->result_amount = (int)$item_layer->result_amount;
            }
        }
        $area_expense_sum['expense_item_cd'] = "";
        $area_expense_sum['expense_item_name'] = "総合計";
        $area_expense_sum['plan_amount'] = $area_expense_sum_plan;
        $area_expense_sum['result_amount'] = $area_expense_sum_result;
        $convention_detail = new stdClass();
        $convention_detail->area_expense_sum = $area_expense_sum;
        $convention_detail->area_expense = $area_expense;
        $result['convention_detail'] = $convention_detail;
        return $result;
    }

    //List Convention Detail
    public function getConventionDetail($request)
    {
        $result = [];
        $convention_detail = $this->repo->getDetailConventionByConventionID($request);
        if (isset($convention_detail->convention_id)) {
            $request->convention_id = $convention_detail->convention_id;
            $result['status_report_approval'] = isset($convention_detail->schedule_id) ? $this->checkStatusReport($convention_detail->schedule_id, $convention_detail->start_date) : false;
            //get detail convention plan
            $result['convention_plan'] = $this->repo->getUserApprovalConventionPlan($request->convention_id, $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_plan)->definition_value ?? null, null);
            //get detail convention result
            $result['convention_result'] = $this->repo->getUserApprovalConventionPlan($request->convention_id, $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_result)->definition_value ?? null, null);
            $input_deadline_convention = $this->repo->getInputDeadlineConvention($this->display_option_name);
            $date_now = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
            if (isset($input_deadline_convention->parameter_value)) {
                $end_date = date("Y-m-d", strtotime("-" . $input_deadline_convention->parameter_value . " months", strtotime($date_now)));
            } else {
                $end_date = $date_now;
            }
            $target_user_org = $this->repo->getTargetUserOrg($request->user_login, $request->convention_id);
            $convention_detail->user_org = isset($target_user_org->user_cd) ? true : false;
            $convention_detail->end_date = $end_date;
            $convention_detail->convention_document = $this->repo->getDocumentConvention($request->convention_id);
            $convention_detail->convention_target_org = $this->repo->getConventionTargetOrg($request->convention_id);
            $convention_file = $this->repo->getConventionFile($request->convention_id);
            if (count($convention_file) > 0) {
                foreach ($convention_file as $item) {
                    $item->url_path = $this->encodeBase64String($item->file_name);
                }
            }
            $convention_detail->convention_file = $convention_file;
            $convention_detail->convention_attendee = $this->repo->getConventionAttendee($request->convention_id);
            $convention_detail->convention_product = isset($convention_detail->convention_product) ? json_decode($convention_detail->convention_product) : [];
            $area_expense = $this->repo->getConventionAreaExpense($request->convention_id);
            $area_expense_sum_plan = 0;
            $area_expense_sum_result = 0;
            if (count($area_expense) > 0) {
                foreach ($area_expense as $item_layer) {
                    $layer_2 = $this->repo->getConventionAreaExpenseLayer($item_layer->expense_item_cd, $request->convention_id);
                    if (count($layer_2) > 0) {
                        foreach ($layer_2 as $item) {
                            if ((int)$item->layer_num < 3) {
                                $layer_3 = $this->repo->getConventionAreaExpenseLayer($item->expense_item_cd, $request->convention_id);
                                foreach ($layer_3 as $item_layer_3) {
                                    $item_layer_3->plan_amount = $item_layer_3->plan_amount ? (int)$item_layer_3->plan_amount : null;
                                    $item_layer_3->result_amount = $item_layer_3->result_amount ? (int)$item_layer_3->result_amount : null;
                                }
                                $item->layer_3 = $layer_3;
                            }
                            $item->plan_amount = $item->plan_amount ? (int)$item->plan_amount : null;
                            $item->result_amount = $item->result_amount ? (int)$item->result_amount : null;
                        }
                    }
                    $item_layer->layer_2 = $layer_2;
                    $area_expense_sum_plan += $item_layer->plan_amount;
                    $area_expense_sum_result += $item_layer->result_amount;
                    $item_layer->plan_amount = (int)$item_layer->plan_amount;
                    $item_layer->result_amount = (int)$item_layer->result_amount;
                }
            }
            $convention_occupation_type = $this->repo->getConventionOccupationType();
            if (count($convention_occupation_type) > 0) {
                $convention_start_date = $convention_detail->start_date;
                $lecture_follow_up = $this->attend->getLectureFollowUp()->parameter_value ?? 0;
                $convention_start_date_add_two_day = Carbon::parse($convention_start_date)->addDays($lecture_follow_up)->format('Y-m-d');
                foreach ($convention_occupation_type as $item_convention_occupation_type) {
                    //get occupation type
                    switch ($item_convention_occupation_type->occupation_type) {
                        case '10':
                            $data_item = $this->repo->getConventionOrderType10($convention_detail->convention_id, $item_convention_occupation_type->occupation_type);
                            $count_follow = $this->repo->getFollowConventionOrderType10($convention_detail->convention_id, $item_convention_occupation_type->medical_staff_cd, $convention_start_date, $convention_start_date_add_two_day);
                            array_push($data_item, array("status_item_cd" => "", "sum_count_user" => count($count_follow)));
                            $item_convention_occupation_type->data_item = $data_item;
                            break;
                        case '20':
                        case '30':
                        case '40':
                        case '50':
                            $data_item = $this->repo->getConventionOrderType20($convention_detail->convention_id, $item_convention_occupation_type->occupation_type, $item_convention_occupation_type->medical_staff_cd);
                            $count_follow = $this->repo->getFollowConventionOrderType10($convention_detail->convention_id, $item_convention_occupation_type->medical_staff_cd, $convention_start_date, $convention_start_date_add_two_day);
                            array_push($data_item, array("status_item_cd" => "", "sum_count_user" => count($count_follow)));
                            $item_convention_occupation_type->data_item = $data_item;
                            break;
                        case '60':
                        case '70':
                            $data_item = $this->repo->getConventionOrderType60($convention_detail->convention_id, $item_convention_occupation_type->occupation_type);
                            array_push($data_item, array("status_item_cd" => "", "sum_count_user" => 0));
                            $item_convention_occupation_type->data_item = $data_item;
                            break;
                        default:
                            $data_item = [];
                    }
                }
            }
            $area_expense_sum['expense_item_cd'] = "";
            $area_expense_sum['expense_item_name'] = "総合計";
            $area_expense_sum['plan_amount'] = $area_expense_sum_plan;
            $area_expense_sum['result_amount'] = $area_expense_sum_result;
            $convention_detail->area_expense_sum = $area_expense_sum;
            $convention_detail->area_expense = $area_expense;
            $convention_detail->convention_occupation_type = $convention_occupation_type;
            $result['convention_detail'] = $convention_detail;
            $definition_label_convention = $convention_detail->status_type > 30 ? $this->definition_label_convention_result : $this->definition_label_convention_plan;
            $user_approval = $this->repo->getLayerUserApprovalConvention($request->user_login, $request->convention_id, $this->variable->getVariableStatusTypeApprovalRequest($definition_label_convention)->definition_value ?? null, null);
            $result['user_approval'] = count($user_approval) > 0 ? 1 : 0;
            $result['layer_user_approval'] = $user_approval;
            $request_type = $this->variable->getVariableStatusTypeApprovalRequest($definition_label_convention)->definition_value ?? null;
            $active_layer_approval = $this->repo->getApprovalRequest($request->convention_id, $request_type);
            $result['active_layer_approval'] = isset($active_layer_approval->request_id) ? $active_layer_approval->active_layer_num : 1;
        }
        return $result;
    }

    //copy submit convention
    public function copyConventionDetail($request)
    {
        $convention_detail = $this->repo->getCopyConvention($request->convention_id);
        $convention_detail->convention_product = isset($convention_detail->convention_product) ? json_decode($convention_detail->convention_product) : [];
        $convention_detail->convention_target_org = $this->repo->getConventionTargetOrg($request->convention_id);
        $area_expense = $this->repo->getConventionAreaExpense($request->convention_id);
        $area_expense_sum_plan = 0;
        $area_expense_sum_result = 0;
        if (count($area_expense) > 0) {
            foreach ($area_expense as $item_layer) {
                $layer_2 = $this->repo->getConventionAreaExpenseLayer($item_layer->expense_item_cd, $request->convention_id);
                if (count($layer_2) > 0) {
                    foreach ($layer_2 as $item) {
                        if ((int)$item->layer_num < 3) {
                            $layer_3 = $this->repo->getConventionAreaExpenseLayer($item->expense_item_cd, $request->convention_id);
                            foreach ($layer_3 as $item_layer_3) {
                                $item_layer_3->plan_amount = $item_layer_3->plan_amount ? (int)$item_layer_3->plan_amount : null;
                                $item_layer_3->result_amount = $item_layer_3->result_amount ? (int)$item_layer_3->result_amount : null;
                            }
                            $item->layer_3 = $layer_3;
                        }
                        $item->plan_amount = $item->plan_amount ? (int)$item->plan_amount : null;
                        $item->result_amount = $item->result_amount ? (int)$item->result_amount : null;
                    }
                }
                $item_layer->layer_2 = $layer_2;
                $area_expense_sum_plan += $item_layer->plan_amount;
                $area_expense_sum_result += $item_layer->result_amount;
                $item_layer->plan_amount = (int)$item_layer->plan_amount;
                $item_layer->result_amount = (int)$item_layer->result_amount;
            }
        }
        $area_expense_sum['expense_item_cd'] = "";
        $area_expense_sum['expense_item_name'] = "総合計";
        $area_expense_sum['plan_amount'] = $area_expense_sum_plan;
        $area_expense_sum['result_amount'] = $area_expense_sum_result;
        $convention_detail->area_expense_sum = $area_expense_sum;
        $convention_detail->area_expense = $area_expense;
        return $convention_detail;
    }

    //copy submit convention
    public function saveConventionDetail($request)
    {
        //1
        // create schedule
        $request->schedule_id = $this->createdSchedule($request)->schedule_id;
        // create convention
        $create_convention = $this->repo->createdConvention($request);
        $request->convention_id_new = $create_convention->convention_id;
        //update convetion product
        $this->saveConventionProduct($request);
        //update convention target_org
        $this->saveConventionTargetOrg($request);
        //save Convention Plan ExpenseItem
        $this->saveConventionPlanExpenseItem($create_convention->convention_id);
        return $create_convention->convention_id;
    }

    //select seris convention
    public function selectionSeriesConventionDetail($request)
    {
        $convention_detail = $this->repo->selectionSeriesConventionDetail($request->convention_id);
        $convention_detail->convention_product = $convention_detail->convention_product ? json_decode($convention_detail->convention_product) : [];
        $convention_detail->convention_target_org = $this->repo->getConventionTargetOrg($request->convention_id);
        return $convention_detail;
    }

    //select seris convention
    public function updateConventionDetail($request)
    {
        //update convetion
        $this->repo->updateConventionPlan($request);
        //update convetion product
        $this->saveConventionProduct($request);
        //update convention target_org
        $this->saveConventionTargetOrg($request);
    }

    //save Plan Convention Detail
    public function saveConventionPlanExpenseItem($convention_id)
    {
        //2
        $convention_plan_expense_item_layer_1 = $this->repo->allConventionExpenseItem();
        if (count($convention_plan_expense_item_layer_1) > 0) {
            foreach ($convention_plan_expense_item_layer_1 as $item_layer_1) {
                // $this->repo->saveConventionPlanExpenseItem($convention_id, $item_layer_1->expense_item_cd);
                $convention_plan_expense_item_layer_2 = $this->repo->allConventionExpenseItemChildren($item_layer_1->expense_item_cd);
                if (count($convention_plan_expense_item_layer_2) > 0) {
                    foreach ($convention_plan_expense_item_layer_2 as $item_layer_2) {
                        if ((int)$item_layer_2->layer_num < 3) {
                            $convention_plan_expense_item_layer_3 = $this->repo->allConventionExpenseItemChildren($item_layer_2->expense_item_cd);
                            if (count($convention_plan_expense_item_layer_3) > 0) {
                                foreach ($convention_plan_expense_item_layer_3 as $item_layer_3) {
                                    $this->repo->saveConventionPlanExpenseItem($convention_id, $item_layer_3->expense_item_cd);
                                }
                            }
                        } else {
                            $this->repo->saveConventionPlanExpenseItem($convention_id, $item_layer_2->expense_item_cd);
                        }
                    }
                }
            }
        }
    }

    //save Plan Convention Detail
    public function savePlanConventionDetail($request)
    {
        if (!$request->convention_id) {
            // create convention
            $request->convention_id = $this->saveConventionDetail($request);
        } else {
            //update convetion
            $this->updateConventionDetail($request);
            //update schedule
            $schedule['start_date'] = $request->start_date;
            $schedule['start_time'] = $request->start_time;
            $schedule['end_time'] = $request->end_time;
            $schedule['all_day_flag'] = 0;
            $schedule['title'] = $request->convention_name;
            $schedule['schedule_id'] = $request->schedule_id;
            $this->schedule->updateScheduleConvention($schedule);
            //save Convention Attendee
            if ($request->convention_attendee) {
                if (count($request->convention_attendee) > 0) {
                    foreach ($request->convention_attendee as $item) {
                        $item_convention_attendee = (object)$item;
                        $this->repo->updateConventionAttendee($request->convention_id, $item_convention_attendee);
                    }
                }
            }

            // save Convention Area Expense 
        }
        // save Convention File
        $this->addConventionFile($request);
        //save Convention Document 
        $this->saveConventionDocument($request);
        //save schedule common subject
        if ($request->convention_target_org) {
            if (count($request->convention_target_org) > 0) {
                $this->repo->deleteScheduleCommonSubject($request->schedule_id);
                foreach ($request->convention_target_org as $item_convention_target_org) {
                    $this->repo->saveScheduleCommonSubject($request->schedule_id, $item_convention_target_org['org_cd']);
                }
            }
        }
        if ($request->status_type == $this->save_result_convention) {
            $this->addDocumentUsageSituation($request);
        }
        return $this->repo->getDetailConventionByConventionID($request);
    }

    //created schedule
    public function createdSchedule($request)
    {
        $option_type = $this->repo->getDisplayOptionTpye();
        $schedule['schedule_type'] = 30;
        $schedule['start_date'] = $request->start_date;
        $schedule['start_time'] = $request->start_time;
        $schedule['end_time'] = $request->end_time;
        $schedule['title'] = $request->convention_name;
        $schedule['all_day_flag'] = 0;
        $schedule['display_option_type'] = $option_type->display_option_type;
        $schedule['user_cd'] = $request->user_login;
        $this->schedule->addSchedule($schedule);
        return $this->_lastInserted('t_schedule', 'schedule_id');
    }

    // save Plan Convention Area Expense 
    public function saveConventionPlanAreaExpense($request)
    {

        if ($request->area_expense) {
            $request->area_expense = json_decode($request->area_expense);
            if (count($request->area_expense) > 0) {
                foreach ($request->area_expense as $item) {
                    $item_area_expense = (object)$item;
                    $this->repo->updateConventionPlanMount($request->convention_id, $item_area_expense->expense_item_cd, $item_area_expense->plan_amount);
                    if (count($item_area_expense->layer_2) > 0) {
                        foreach ($item_area_expense->layer_2 as $item_layer_2) {
                            $item__layer_2_area_expense = (object)$item_layer_2;
                            if ($item__layer_2_area_expense->layer_num == 3) {
                                $this->repo->updateConventionPlanMountLayer2(
                                    $request->convention_id,
                                    $item__layer_2_area_expense->expense_item_cd,
                                    $item__layer_2_area_expense->option_item_name,
                                    $item__layer_2_area_expense->option_item_content,
                                    $item__layer_2_area_expense->plan_unit_price,
                                    $item__layer_2_area_expense->plan_quantity,
                                    $item__layer_2_area_expense->plan_amount
                                );
                            }
                            if (isset($item__layer_2_area_expense->layer_3)) {
                                foreach ($item__layer_2_area_expense->layer_3 as $item_layer_3) {
                                    $item__layer_3_area_expense = (object)$item_layer_3;
                                    if ($item__layer_3_area_expense->layer_num == 3) {
                                        $this->repo->updateConventionPlanMountLayer2(
                                            $request->convention_id,
                                            $item__layer_3_area_expense->expense_item_cd,
                                            $item__layer_3_area_expense->option_item_name,
                                            $item__layer_3_area_expense->option_item_content,
                                            $item__layer_3_area_expense->plan_unit_price,
                                            $item__layer_3_area_expense->plan_quantity,
                                            $item__layer_3_area_expense->plan_amount
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    // save Convention Area Expense 
    public function saveConventionResultAreaExpense($request)
    {
        if ($request->area_expense) {
            $request->area_expense = json_decode($request->area_expense);
            if (count($request->area_expense) > 0) {
                foreach ($request->area_expense as $item) {
                    $item_area_expense = (object)$item;
                    $this->repo->updateConventionResultMount($request->convention_id, $item_area_expense->expense_item_cd, $item_area_expense->result_amount);
                    if (count($item_area_expense->layer_2) > 0) {
                        foreach ($item_area_expense->layer_2 as $item_layer_2) {
                            $item__layer_2_area_expense = (object)$item_layer_2;
                            if ($item__layer_2_area_expense->layer_num == 3) {
                                $this->repo->updateConventionResultMountLayer2(
                                    $request->convention_id,
                                    $item__layer_2_area_expense->expense_item_cd,
                                    $item__layer_2_area_expense->option_item_name,
                                    $item__layer_2_area_expense->option_item_content,
                                    $item__layer_2_area_expense->result_unit_price,
                                    $item__layer_2_area_expense->result_amount,
                                    $item__layer_2_area_expense->result_quantity
                                );
                            }
                            if (isset($item__layer_2_area_expense->layer_3)) {
                                foreach ($item__layer_2_area_expense->layer_3 as $item_layer_3) {
                                    $item__layer_3_area_expense = (object)$item_layer_3;
                                    if ($item__layer_3_area_expense->layer_num == 3) {
                                        $this->repo->updateConventionResultMountLayer2(
                                            $request->convention_id,
                                            $item__layer_3_area_expense->expense_item_cd,
                                            $item__layer_3_area_expense->option_item_name,
                                            $item__layer_3_area_expense->option_item_content,
                                            $item__layer_3_area_expense->result_unit_price,
                                            $item__layer_3_area_expense->result_amount,
                                            $item__layer_3_area_expense->result_quantity
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    //add Convention File
    public function addConventionFile($request)
    {
        if ($request->convention_file) {
            if (!$request->convention_id) {
                if (count($request->convention_file)) {
                    foreach ($request->convention_file as $item) {
                        $convention_file = (object)$item;
                        $fileData = $this->fileService->uploadFileConvention($convention_file->file);
                        $save_convention_file['convention_id'] = $request->convention_id_new;
                        $save_convention_file['file_id'] = $fileData->file_id;
                        $this->repo->addConventionFile($save_convention_file);
                    }
                }
            } else {
                //update convention product
                if (count($request->convention_file) > 0) {
                    foreach ($request->convention_file as $item) {
                        $convention_file = (object)$item;
                        if ($convention_file->delete_flag == 1) {
                            $fileData = $this->fileService->uploadFileConvention($convention_file->file);
                            $save_convention_file['convention_id'] = $request->convention_id;
                            $save_convention_file['file_id'] = $fileData->file_id;
                            $this->repo->addConventionFile($save_convention_file);
                        } elseif ($convention_file->delete_flag == -1) {
                            $this->repo->deleteConventionFile($request->convention_id, $convention_file->file_id);
                            $this->repo->deleteFile($convention_file->file_id);
                        }
                    }
                }
            }
        }
    }

    //update convention product
    public function saveConventionProduct($request)
    {
        if ($request->convention_product) {
            if (!$request->convention_id) {
                //insert convention product
                if (count($request->convention_product) > 0) {
                    foreach ($request->convention_product as $item_convention_product) {
                        $this->repo->insertConventionProduct($request->convention_id_new, (object)$item_convention_product);
                    }
                }
            } else {
                //update convention product
                if (count($request->convention_product) > 0) {
                    foreach ($request->convention_product as $item) {
                        $item_convention_product = (object)$item;
                        if ($item_convention_product->delete_flag == 1) {
                            $this->repo->insertConventionProduct($request->convention_id, $item_convention_product);
                        } elseif ($item_convention_product->delete_flag == -1) {
                            $this->repo->deleteConventionProduct($request->convention_id, $item_convention_product->product_cd);
                        }
                    }
                }
            }
        }
    }

    //update convention target_org
    public function saveConventionTargetOrg($request)
    {
        if ($request->convention_target_org) {
            if (!$request->convention_id) {
                //insert Convention Target Org
                if (count($request->convention_target_org) > 0) {
                    foreach ($request->convention_target_org as $item_convention_target_org) {
                        $this->repo->createdConventionTargetOrg($request->convention_id_new, (object)$item_convention_target_org);
                    }
                }
            } else {
                //update list Convention Target Org
                if (count($request->convention_target_org) > 0) {
                    foreach ($request->convention_target_org as $item) {
                        $item_convention_target_org = (object)$item;
                        if ($item_convention_target_org->delete_flag == 1) {
                            $this->repo->createdConventionTargetOrg($request->convention_id, $item_convention_target_org);
                        } elseif ($item_convention_target_org->delete_flag == -1) {
                            $this->repo->deleteConventionTargetOrg($request->convention_id, $item_convention_target_org->org_cd);
                        }
                    }
                }
            }
        }
    }

    //update convention document
    public function saveConventionDocument($request)
    {
        if ($request->convention_document) {
            if (!$request->convention_id) {
                //insert Convention Document
                if (count($request->convention_document) > 0) {
                    foreach ($request->convention_document as $item_convention_document) {
                        $this->repo->createdConventionDocument($request->convention_id_new, (object)$item_convention_document);
                    }
                }
            } else {
                //update list Convention Document
                if (count($request->convention_document) > 0) {
                    foreach ($request->convention_document as $item) {
                        $item_convention_document = (object)$item;
                        if ($item_convention_document->delete_flag == 1) {
                            $this->repo->createdConventionDocument($request->convention_id, $item_convention_document);
                        } elseif ($item_convention_document->delete_flag == -1) {
                            $this->repo->deleteConventionDocument($request->convention_id, $item_convention_document->document_id);
                        }
                    }
                }
            }
        }
    }

    //cancel submit convention
    public function cancelSubmitConvention($request)
    {
        return $this->repo->updateStatusTypeConvention($request->convention_id, $request->status_type);
    }

    //cancel submit convention
    public function pendingConvention($request)
    {
        $request_type_report = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_report)->definition_value ?? null;
        $inform_category = $this->repo->getInformCategory($this->category_name_pending_convention);
        $user_create_report = $this->repo->getUserCreateReport($request->convention_detail->schedule_id ?? null);
        if (isset($user_create_report->user_cd)) {
            $this->addInform(
                $user_create_report->user_cd,
                $inform_category->inform_category_cd,
                $request->convention_detail->convention_id,
                $request->convention_detail->convention_name,
                $this->inform_contents_pending
            );
            $user_approval_report = $this->repo->getUserApprovalReport($user_create_report->report_id, $request_type_report);
            if (count($user_approval_report) > 0) {
                foreach ($user_approval_report as $item_approval_report) {
                    $this->addInform(
                        $item_approval_report->approval_user_cd,
                        $inform_category->inform_category_cd,
                        $request->convention_detail->convention_id,
                        $request->convention_detail->convention_name,
                        $this->inform_contents_pending
                    );
                }
            }
        }
        $user_convention_target_org = $this->repo->getUserConventionTargetOrg($request->convention_id);
        if (count($user_convention_target_org) > 0) {
            foreach ($user_convention_target_org as $item_target_org) {
                $this->addInform(
                    $item_target_org->user_cd,
                    $inform_category->inform_category_cd,
                    $request->convention_detail->convention_id,
                    $request->convention_detail->convention_name,
                    $this->inform_contents_pending
                );
            }
        }
        $this->repo->updateInvisibleFlagSchedule($request->convention_detail->schedule_id ?? null);
        return $this->repo->updateStatusTypeConvention($request->convention_id, $request->status_type);
    }

    public function getUserApproval($user_cd){
        return $this->repo->getUserApproval($this->plan_approval_work_type, null, $user_cd);
    }

    //submit plan convention
    public function submitPlanConventionDetail($request)
    {
        $user_approval = $this->repo->getUserApproval($this->plan_approval_work_type, null, $request->convention_detail->plan_user_cd);
        $request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_plan)->definition_value ?? null;
        $status_type = 0;
        //update remand flag convention
        $request->approval_work_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_plan)->definition_value ?? null;
        $this->repo->updateRemandFlagConvention($request->convention_id, $this->un_remand_flag);
        $approval_request = $this->repo->getApprovalRequest($request->convention_id, $request->approval_work_type);
        if (isset($approval_request->request_id)) {
            $this->repo->deleteApprovalRequest($approval_request->request_id);
        }
        //create aproval request convention
        $approval['request_type'] = $request_type;
        $approval['request_target_id'] = $request->convention_id;
        $approval['request_datetime'] = $this->system->getCurrentSystemDateTime();
        $approval['request_user_cd'] = $request->user_login;
        $approval['status_type'] = $status_type;
        $approval['active_layer_num'] = 1;
        $approval_request_conveiton = $this->repo->addApprovalRequest($approval);
        if (count($user_approval) > 0) {
            foreach ($user_approval as $item_user_approval) {
                $approval_detail['request_id'] = $approval_request_conveiton->request_id;
                $approval_detail['layer_num'] = $item_user_approval->approval_layer_num;
                $approval_detail['approval_user_cd'] = $item_user_approval->approval_user_cd;
                $approval_detail['status_type'] = $status_type;
                $approval_detail['approval_datetime'] = $this->system->getCurrentSystemDateTime();
                $approval_detail['comment'] = "";
                $this->repo->addApprovalRequestDetail($approval_detail);
            }
        }
        $user_approval_layer_1 = $this->repo->getUserApprovalConventionPlan($request->convention_id, $request_type, 1);
        // $user_approval_layer_1 = $this->repo->getUserApproval($this->plan_approval_work_type, $this->approval_layer_1, $request->convention_detail->plan_user_cd);
        if (count($user_approval_layer_1) > 0) {
            $inform_category = $this->repo->getInformCategory($this->category_name_submit_plan);
            foreach ($user_approval_layer_1 as $item) {
                $this->addInform($item->approval_user_cd, $inform_category->inform_category_cd, $request->convention_id, $request->convention_name, $this->inform_contents_submit_plan);
            }
        }
        return $this->repo->updateStatusTypeConvention($request->convention_id, $request->status_type);
    }

    //submit plan convention
    public function submitResultConventionDetail($request)
    {
        $user_approval = $this->repo->getUserApproval($this->plan_approval_work_type, null, $request->convention_detail->plan_user_cd);
        $request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_result)->definition_value ?? null;
        $status_type = 0;
        //update remand flag convention
        $this->repo->updateRemandFlagConvention($request->convention_id, $this->un_remand_flag);
        $request->approval_work_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_result)->definition_value ?? null;
        $approval_request = $this->repo->getApprovalRequest($request->convention_id, $request->approval_work_type);
        if (isset($approval_request->request_id)) {
            $this->repo->deleteApprovalRequest($approval_request->request_id);
        }
        //create aproval request convention
        $approval['request_type'] = $request_type;
        $approval['request_target_id'] = $request->convention_id;
        $approval['request_datetime'] = $this->system->getCurrentSystemDateTime();
        $approval['request_user_cd'] = $request->user_login;
        $approval['status_type'] = $status_type;
        $approval['active_layer_num'] = 1;
        $approval_request_conveiton = $this->repo->addApprovalRequest($approval);
        if (count($user_approval) > 0) {
            foreach ($user_approval as $item_user_approval) {
                $approval_detail['request_id'] = $approval_request_conveiton->request_id;
                $approval_detail['layer_num'] = $item_user_approval->approval_layer_num;
                $approval_detail['approval_user_cd'] = $item_user_approval->approval_user_cd;
                $approval_detail['status_type'] = $status_type;
                $approval_detail['approval_datetime'] = $this->system->getCurrentSystemDateTime();
                $approval_detail['comment'] = "";
                $this->repo->addApprovalRequestDetail($approval_detail);
            }
        }
        $user_approval_layer_1 = $this->repo->getUserApprovalConventionPlan($request->convention_id, $request_type, 1);
        // $user_approval_layer_1 = $this->repo->getUserApproval($this->plan_approval_work_type, $this->approval_layer_1, $request->convention_detail->plan_user_cd);
        if (count($user_approval_layer_1) > 0) {
            $inform_category = $this->repo->getInformCategory($this->category_name_submit_plan);
            foreach ($user_approval_layer_1 as $item) {
                $this->addInform($item->approval_user_cd, $inform_category->inform_category_cd, $request->convention_id, $request->convention_name, $this->inform_contents_approval);
            }
        }
        $this->addDocumentUsageSituation($request);
        return $this->repo->updateStatusTypeConvention($request->convention_id, $request->status_type);
    }

    //add Document Usage Situation
    public function addDocumentUsageSituation($request)
    {
        if ($request->convention_document) {
            if (count($request->convention_document) > 0) {
                $document_usage_type = $this->repo->getDocumentUsageType($this->definition_label_convention_usage_type);
                foreach ($request->convention_document as $item) {
                    $item_convention_document = (object)$item;
                    if ($item_convention_document->delete_flag == -1) {
                        $this->repo->deleteDocumentUsageSituation($request->convention_id, $document_usage_type, $item_convention_document->document_id);
                    } else {
                        $this->repo->createDocumentUsageSituation($request, $request->convention_id, $document_usage_type, $item_convention_document->document_id);
                    }
                }
            }
        }
    }

    //add Inform
    public function addInform($inform_user_cd, $inform_category_cd, $convention_id, $convention_name, $inform_contents)
    {
        $not_received_inform_list = $this->repo->installed($inform_user_cd, $inform_category_cd);
        //Check if the user is set to receive notification type
        $check_not_received = 0;
        foreach($not_received_inform_list as $value){
            if($value->checked == 0){
                $check_not_received++;
            }
        }
        if($check_not_received == 0){
            $infrom['inform_category_cd'] = $inform_category_cd;
            $infrom['inform_datetime'] = $this->system->getCurrentSystemDateTime();
            $infrom['inform_contents'] = $convention_name . $inform_contents;
            $infrom['inform_user_cd'] = $inform_user_cd;
            $infrom_id = $this->repo->addInform($infrom);
            $inform_parameter['parameter_key'] = $this->parameter_key_convention;
            $inform_parameter['parameter_value'] = $convention_id;
            $inform_parameter['inform_id'] = $infrom_id;
            $this->repo->addInformParameter($inform_parameter);
        }
    }

    //add Inform
    public function cancelConvention($request)
    {
        return $this->repo->updateStatusTypeConvention($request->convention_id, $request->status_type);
    }

    //remand convention
    public function remandConvention($request)
    {
        //check type convention
        if ($request->status_type == 10) {
            $request->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_plan)->definition_value ?? null;
        } elseif ($request->status_type == 40) {
            $request->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_result)->definition_value ?? null;
        }
        //get inform category cd
        $inform_category = $this->repo->getInformCategory($this->category_name_remand);
        $approval_request = $this->repo->getApprovalRequest($request->convention_id, $request->request_type);
        //update remand flag convention
        $this->repo->updateRemandFlagConvention($request->convention_id, $this->remand_flag);
        //update status convention
        $this->repo->updateStatusTypeConvention($request->convention_id, $request->status_type);
        //remand convention finnal
        $this->repo->updateConventionFinal($approval_request->request_id, $this->remand_convention);
        $this->repo->updateConvention($approval_request->request_id, $request->user_login, $this->remand_convention, $request->comment, $request->active_layer_approval);
        $this->addInform($request->convention_detail->plan_user_cd, $inform_category->inform_category_cd, $request->convention_id, $request->convention_name, $this->inform_contents_remand);
        return true;
    }

    //approval convention
    public function approvalConvention($request)
    {
        //check type convention
        if ($request->status_type == $this->status_submit_plan) {
            $request->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_plan)->definition_value ?? null;
        } elseif ($request->status_type == $this->status_submit_result) {
            $request->request_type = $this->variable->getVariableStatusTypeApprovalRequest($this->definition_label_convention_result)->definition_value ?? null;
        }
        //get inform category cd
        $inform_category = $this->repo->getInformCategory($this->category_name_approval);
        $approval_request = $this->repo->getApprovalRequest($request->convention_id, $request->request_type);
        $content_approval = $this->inform_contents_approval;
        $layer_num_approval_finnal = $this->repo->getLayerNumApprovalFinnal();
        $user_approval = $this->repo->checkUserApprovalFinal($request->convention_id, $request->request_type, $request->user_login);
        if ($request->active_layer_approval == $layer_num_approval_finnal->parameter_value && is_object($user_approval)) {
            $this->repo->updateConventionFinal($request->convention_id, $this->approval_convention, $this->plan_approval_work_type);
            //update status convention
            $this->repo->updateStatusTypeConvention($request->convention_id, $request->status_type);
            $content_approval = $this->inform_contents_approval_final;
            $this->repo->updateConvention($approval_request->request_id, $request->user_login, $this->approval_convention, $request->comment, $request->active_layer_approval);
            $convention_expense_detail = $this->repo->getConventionExpenseDetailByConventionId($request->convention_id);
            foreach($convention_expense_detail as $value){
                $this->repo->updateConventionResultMountByLastApproval(
                    $request->convention_id,
                    $value->expense_item_cd,
                    $value->plan_unit_price,
                    $value->plan_amount,
                    $value->plan_quantity
                );
            }
            $this->addInform($request->convention_detail->plan_user_cd, $inform_category->inform_category_cd, $request->convention_id, $request->convention_name, $content_approval);
        } else {
            $this->repo->updateConvention($approval_request->request_id, $request->user_login, $this->approval_convention, $request->comment, $request->active_layer_approval);
            $this->repo->updateActiveApprovalRequest($approval_request->request_id, $request->active_layer_approval + 1);
            // $list_user_approval = $this->repo->getUserApproval($this->plan_approval_work_type, $request->active_layer_approval + 1, $request->convention_detail->plan_user_cd);
            $list_user_approval = $this->repo->getUserApprovalConventionPlan($request->convention_id, $request->request_type, $request->active_layer_approval + 1);
            if (count($list_user_approval) > 0) {
                foreach ($list_user_approval as $item) {
                    $this->addInform($item->approval_user_cd, $inform_category->inform_category_cd, $request->convention_id, $request->convention_name, $content_approval);
                }
            }
        }
        return true;
    }

    //check Status Report by ScheduleID
    public function checkStatusReport($schedule_id, $start_date)
    {
        $schedule = $this->schedule->getStatusReport($schedule_id, $start_date);
        $report_id = $schedule->report_id ?? null;
        $repo = new DailyReportRepository();
        $status = $repo->getStatusTypeReport($report_id);
        $status_report = false;
        if (isset($status->status_type)) {
            $status_report = $status->status_type != 1 ? true : false;
        }
        return $status_report;
    }

    //Delete Convention
    public function deleteConvention($request)
    {
        return $this->repo->deleteConvention($request->convention_id);
    }
}
