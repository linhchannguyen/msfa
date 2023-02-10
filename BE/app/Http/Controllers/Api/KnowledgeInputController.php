<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\KnowledgeInputRequest\ApproveRequest;
use App\Http\Requests\Api\KnowledgeInputRequest\DeleteKnowledgeRequest;
use App\Http\Requests\Api\KnowledgeInputRequest\GetKnowledgeInputRequest;
use App\Http\Requests\Api\KnowledgeInputRequest\GetScreenDataRequest;
use App\Http\Requests\Api\KnowledgeInputRequest\NonePublicRequest;
use App\Http\Requests\Api\KnowledgeInputRequest\PublicKnowledgeRequest;
use App\Http\Requests\Api\KnowledgeInputRequest\RejectionRequest;
use App\Http\Requests\Api\KnowledgeInputRequest\RemandRequest;
use App\Http\Requests\Api\KnowledgeInputRequest\UpdateAndCreateRequest;
use App\Services\KnowledgeInputService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class KnowledgeInputController extends Controller
{
    private $service;

    public function __construct(KnowledgeInputService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     *  @OA\GET(
     *    path="/api/knowledge-input/get-screen-data",
     *    operationId="getScreenDataKnowledgeInput",
     *    tags={"F01-S03"},
     *    summary="Knowledge Input",
     *    description="Get Screen Data",
     *    security={{"jwt": {}}},
     *    @OA\Response(response=200, description="Response successfully",
     *    @OA\JsonContent(
     *    type="object",
     *    example={
     *               "status": "200",
     *               "message": "success",
     *               "data": {
     *                           "status": {
     *                               {
     *                                   "definition_label": "作成中",
     *                                   "definition_value": "10"
     *                               }
     *                           },
     *                           "category": {
     *                               {
     *                                   "knowledge_category_cd": "10",
     *                                   "knowledge_category_name": "施策進捗"
     *                               }
     *                           },
     *                           "medical_subjects": {
     *                               {
     *                                   "medical_subjects_group_cd": "1A",
     *                                   "medical_subjects_group_name": "内科"
     *                               }
     *                           },
     *                           "status_approval": {
     *                               {
     *                                   "definition_value": "0",
     *                                   "definition_label": "承認待ち"
     *                               }
     *                           },
     *                           "active_point": {
     *                             {
     *                                 "definition_label": "100",
     *                                 "definition_value": "10"
     *                             },
     *                            "approval_work_type": "4",
     *                            "approval_layer_num": "4"
     *                        }
     *                  }
     *           }
     *        )
     *    ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *  )
     */
    public function getScreenData(Request $request)
    {
        $data =  $this->service->getScreenData();
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\GET(
     *    path="/api/knowledge-input",
     *    operationId="getKnowledgeInput",
     *    tags={"F01-S03"},
     *    summary="Knowledge Input",
     *    description="Get Knowledge Input",
     *    security={{"jwt": {}}},
     *    @OA\Parameter(name="knowledge_id",in="query", @OA\Schema(type="string")),
     *    @OA\Response(response=200, description="Response successfully",
     *    @OA\JsonContent(
     *    type="object",
     *    example={
     *               "status": "200",
     *               "message": "success",
     *               "data": {
     *                           "status": "200",
     *                           "message": "success",
     *                           "data": {
     *                                   "status": {
     *                                       "knowledge_status_type": "20",
     *                                       "definition_label": "承認確認中"
     *                                   },
     *                                   "knowledgeTab1AndTab3": {
     *                                       "knowledge_id": 670,
     *                                       "anonymous_flag": 0,
     *                                       "title": "kute",
     *                                       "knowledge_category_cd": "10",
     *                                       "product_name": "AAA10mg",
     *                                       "facility_short_name": "田口医師会保健医療センター（札幌市中央区）",
     *                                       "facility_cd": "011010002",
     *                                       "person_cd": "01101002",
     *                                       "person_name": "和田 晴生",
     *                                       "medical_subjects_group_cd": "1B",
     *                                       "knowledge_category_name": "施策進捗",
     *                                       "medical_subjects_group_name": "外科",
     *                                       "create_user_cd": "10008",
     *                                       "product_cd": "AAA-10",
     *                                       "users": {
     *                                           {
     *                                               "user_cd": "10002",
     *                                               "user_name": "安永 みづほ"
     *                                           }
     *                                       },
     *                                       "contents": "haha",
     *                                       "submit_comment": "keke",
     *                                       "comment": {
     *                                           {
     *                                               "layer_num": 1,
     *                                               "comment": null
     *                                           }
     *                                       },
     *                                       "check_knowledge_admin": 1,
     *                                       "active_point_cd": null,
     *                                       "first_release_datetime": null,
     *                                       "release_datetime": null,
     *                                       "release_flag": 0,
     *                                       "create_datetime": "2022-03-22 00:00:00"
     *                                   },
     *                                   "create_user_cd": "10008",
     *                                   "last_approver_layer": {
     *                                       {
     *                                           "approval_user_cd": "test",
     *                                           "approval_layer_num": 4
     *                                       }
     *                                   },
     *                                   "other_last_approver_layer": {
     *                                       {
     *                                           "approval_user_cd": "10001",
     *                                           "approval_layer_num": 1
     *                                       }
     *                                   },
     *                                   "is_knowledge_admin": true,
     *                                   "note": {
     *                                       {
     *                                           "definition_value": "10",
     *                                           "definition_label": "面談"
     *                                       }
     *                                   },
     *                                   "time_line": {},
     *                                   "data_approval": {
     *                                       "approval_request": {
     *                                           "request_id": 1789,
     *                                           "request_type": "60",
     *                                           "request_target_id": 670,
     *                                           "request_user_cd": "10008",
     *                                           "request_datetime": "2022-03-22 18:58:33",
     *                                           "status_type": "0"
     *                                       },
     *                                       "approval_request_detail": {
     *                                           {
     *                                               "request_id": 1789,
     *                                               "layer_num": 1,
     *                                               "approval_user_cd": "10001",
     *                                               "status_type": "0",
     *                                               "comment": null,
     *                                               "user_name": "西嶋 洋明"
     *                                       }
     *                                 }
     *                            }
     *                      }
     *                }
     *           }
     *        )
     *    ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *  )
     */
    public function getKnowledgeInput(GetKnowledgeInputRequest $request)
    {
        $knowledge_id = $request->knowledge_id;
        $approval_work_type = $request->approval_work_type;
        $approval_layer_num = $request->approval_layer_num;
        $user_cd = $this->getCurrentUser() ?? $request->user_cd;
        $data =  $this->service->getKnowledgeInput($knowledge_id, $user_cd, $approval_work_type, $approval_layer_num);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\Post(
     *      path="/api/knowledge-input/update-and-create",
     *      operationId="updateAndCreate",
     *      tags={"F01-S03"},
     *      summary="Knowledge Input",
     *      description="Update And Create",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Update And Create Param",
     *          required=true, 
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      description="knowledge_id",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="mode_screen",
     *                      type="string",
     *                      description="mode_screen",
     *                      default="create"
     *                  ),
     *                  @OA\Property(
     *                      property="anonymous_flag",
     *                      type="number",
     *                      description="anonymous_flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="approval_work_type",
     *                      type="string",
     *                      description="approval_work_type",
     *                      default="4"
     *                  ),
     *                  @OA\Property(
     *                      property="approval_layer_num",
     *                      type="string",
     *                      description="approval_layer_num",
     *                      default="4"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      description="title",
     *                      default="haha"
     *                  ),
     *                  @OA\Property(
     *                      property="product_cd",
     *                      type="string",
     *                      description="product_cd",
     *                      default="AAA-100"
     *                  ),
     *                  @OA\Property(
     *                      property="knowledge_category_cd",
     *                      type="string",
     *                      description="knowledge_category_cd",
     *                      default="20"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="facility_cd",
     *                      default="011010001"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      description="facility_short_name",
     *                      default="浅田市民病院（札幌市中央区）"
     *                  ),
     *                 @OA\Property(
     *                      property="medical_subjects_group_cd",
     *                      type="string",
     *                      description="medical_subjects_group_cd",
     *                      default="1B"
     *                  ),
     *                 @OA\Property(
     *                      property="medical_subjects_group_name",
     *                      type="string",
     *                      description="medical_subjects_group_name",
     *                      default="外科"
     *                  ),
     *                 @OA\Property(
     *                      property="users",
     *                      type="string",
     *                      description="users",
     *                      default="[{ 'user_cd': '10002' }]"
     *                  ),
     *                  @OA\Property(
     *                      property="time_line",
     *                      type="string",
     *                      description="time_line",
     *                      default="[{ 'data': 
     *                          {
     *                             'timeline_id': 9,
     *                             'channel_type': '40',
     *                             'channel_id': 407,
     *                             'delete_flag': 0,
     *                             'comment' : null
     *                          }
     *                      }]"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Saved normally.",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "201",
     *                      "message": "正常に保存しました。",
     *                      "data": {
     *                          "knowledge_id": 314
     *                      }
     *                 }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function updateAndCreate(UpdateAndCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $knowledge_id = $request->knowledge_id;
            $request->user_cd = $this->getCurrentUser();
            $data = $this->service->UpdateAndCreate($knowledge_id, $request);
            $knowledge_id = $data['knowledge_id'];
            DB::commit();
            return $this->responseCreated(__('knowledgeInput.update'), ['knowledge_id' => $knowledge_id]);
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('knowledgeInput.system_error'));
        }
    }

    /**
     *  @OA\Post(
     *      path="/api/knowledge-input/submit",
     *      operationId="submit",
     *      tags={"F01-S03"},
     *      summary="Knowledge Input",
     *      description="Submit",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Submit Param",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      description="knowledge_id",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="facility_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="anonymous_flag",
     *                      type="number",
     *                      description="anonymous_flag",
     *                      default="0"
     *                  ),
     *                   @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      description="title",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="knowledge_category_cd",
     *                      type="string",
     *                      description="knowledge_category_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="product_cd",
     *                      type="string",
     *                      description="product_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      description="facility_short_name",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      description="person_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="person_name",
     *                      type="string",
     *                      description="person_name",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="medical_subjects_group_cd",
     *                      type="string",
     *                      description="medical_subjects_group_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="medical_subjects_group_name",
     *                      type="string",
     *                      description="medical_subjects_group_name",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="contents",
     *                      type="string",
     *                      description="contents",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="submit_comment",
     *                      type="string",
     *                      description="submit_comment",
     *                      default=""
     *                  ),
     *                 @OA\Property(
     *                      property="users",
     *                      type="string",
     *                      description="users",
     *                      default=""
     *                  ),
     *                   @OA\Property(
     *                      property="approval_work_type",
     *                      type="string",
     *                      description="approval_work_type",
     *                      default="4"
     *                  ),
     *                  @OA\Property(
     *                      property="approval_layer_num",
     *                      type="string",
     *                      description="approval_layer_num",
     *                      default="4"
     *                  ),
     *                  @OA\Property(
     *                      property="time_line",
     *                      type="string",
     *                      description="time_line",
     *                      default="[{ 'data': 
     *                          {
     *                             'timeline_id': 9,
     *                             'channel_type': '40',
     *                             'channel_id': 407,
     *                             'delete_flag': 0,
     *                             'comment' : null
     *                          }
     *                      }]"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="The source of knowledge has been submitted.",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "201",
     *                      "message": "ナレッジ元が提出されました。",
     *                      "data": {
     *                          "knowledge_id": 314
     *                      }
     *                 }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function submit(UpdateAndCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->user_cd = $this->getCurrentUser();
            $data = $this->service->UpdateAndCreate($request->knowledge_id, $request);
            $request->knowledge_id = $data['knowledge_id'] ?? 0;
            $request->knowledge_status_temp = $data['knowledge_status_type']->definition_value ?? 0;
            $request->inform_contents =  __('knowledgeInput.submit');
            $user_approval_knowledge = $this->service->getUserApproval($request->user_cd);
            if(empty($user_approval_knowledge)){
                return $this->responseErrorValidate(__('knowledgeInput.no_approval'));
            }
            $status = $this->service->submit($request);
            if (!$status) {
                return $this->responseErrorValidate(__('knowledgeInput.button_role', ["0" => "提出"]));
            }
            DB::commit();
            return $this->responseCreated(__('knowledgeInput.submit'),  ['knowledge_id' => $request->knowledge_id]);
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('knowledgeInput.system_error'));
        }
    }

    /**
     *  @OA\Post(
     *      path="/api/knowledge-input/approve",
     *      operationId="approve",
     *      tags={"F01-S03"},
     *      summary="Knowledge Input",
     *      description="Approval",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="approve Param",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      description="knowledge_id",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="create_user_cd",
     *                      type="string",
     *                      description="create_user_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="approval_work_type",
     *                      type="string",
     *                      description="approval_work_type",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="approval_layer_num",
     *                      type="string",
     *                      description="approval_layer_num",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="active_layer_num",
     *                      type="string",
     *                      description="active_layer_num",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="request_id",
     *                      type="string",
     *                      description="request_id",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="comment_orther_approval",
     *                      type="string",
     *                      description="comment_orther_approval",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="mode_screen",
     *                      type="string",
     *                      description="mode_screen",
     *                      default="approval"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="The source of knowledge has been approved.",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "201",
     *                      "message": "ナレッジ元が承認されました。",
     *                      "data": {
     *                      }
     *                 }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */

    public function approve(ApproveRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->inform_contents =  __('knowledgeInput.submit');
            $request->message = __('knowledgeInput.approve_message');
            $request->user_cd = $this->getCurrentUser();
            $status = $this->service->approve($request);
            if (!$status) {
                return $this->responseErrorValidate(__('knowledgeInput.button_role', ["0" => "承認"]));
            }
            DB::commit();
            return $this->responseCreated(__('knowledgeInput.inform_contents_approve'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('knowledgeInput.system_error'));
        }
    }

    /**
     *  @OA\Post(
     *      path="/api/knowledge-input/remand",
     *      operationId="remand",
     *      tags={"F01-S03"},
     *      summary="Knowledge Input",
     *      description="Remand",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Remand Param",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      description="knowledge_id",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="create_user_cd",
     *                      type="string",
     *                      description="create_user_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="mode_screen",
     *                      type="string",
     *                      description="mode_screen",
     *                      default="approval"
     *                  ),
     *                  @OA\Property(
     *                      property="approval_work_type",
     *                      type="string",
     *                      description="approval_work_type",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="approval_layer_num",
     *                      type="string",
     *                      description="approval_layer_num",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="active_layer_num",
     *                      type="string",
     *                      description="active_layer_num",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="anonymous_flag",
     *                      type="number",
     *                      description="anonymous_flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      description="title",
     *                      default="haha"
     *                  ),
     *                  @OA\Property(
     *                      property="product_cd",
     *                      type="string",
     *                      description="product_cd",
     *                      default="AAA-100"
     *                  ),
     *                  @OA\Property(
     *                      property="knowledge_category_cd",
     *                      type="string",
     *                      description="knowledge_category_cd",
     *                      default="20"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="facility_cd",
     *                      default="011010001"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      description="facility_short_name",
     *                      default="浅田市民病院（札幌市中央区）"
     *                  ),
     *                 @OA\Property(
     *                      property="medical_subjects_group_cd",
     *                      type="string",
     *                      description="medical_subjects_group_cd",
     *                      default="1B"
     *                  ),
     *                 @OA\Property(
     *                      property="medical_subjects_group_name",
     *                      type="string",
     *                      description="medical_subjects_group_name",
     *                      default="外科"
     *                  ),
     *                 @OA\Property(
     *                      property="users",
     *                      type="string",
     *                      description="users",
     *                      default="[{ 'user_cd': '10002' }]"
     *                  ),
     *                  @OA\Property(
     *                      property="time_line",
     *                      type="string",
     *                      description="time_line",
     *                      default="[{ 'data': 
     *                          {
     *                             'timeline_id': 9,
     *                             'channel_type': '40',
     *                             'channel_id': 407,
     *                             'delete_flag': 0,
     *                             'comment' : null
     *                          }
     *                      }]"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="The original knowledge has been remanded.",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "201",
     *                      "message": "ナレッジ元が差し戻されました。",
     *                      "data": {
     *                      }
     *                 }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */

    public function remand(RemandRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->inform_contents =  __('knowledgeInput.inform_contents_remand');
            $request->user_cd = $this->getCurrentUser();
            $status = $this->service->remand($request);
            if (!$status) {
                return $this->responseErrorValidate(__('knowledgeInput.button_role', ["0" => "差戻"]));
            }
            DB::commit();
            return $this->responseCreated(__('knowledgeInput.inform_contents_remand'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('knowledgeInput.system_error'));
        }
    }

    /**
     *  @OA\Post(
     *      path="/api/knowledge-input/public",
     *      operationId="public",
     *      tags={"F01-S03"},
     *      summary="Knowledge Input",
     *      description="Public",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Public Param",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      description="knowledge_id",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="create_user_cd",
     *                      type="string",
     *                      description="create_user_cd",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="approval_work_type",
     *                      type="string",
     *                      description="approval_work_type",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="approval_layer_num",
     *                      type="string",
     *                      description="approval_layer_num",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="mode_screen",
     *                      type="string",
     *                      description="mode_screen",
     *                      default="approval"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Your knowledge has been published.",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "201",
     *                      "message": "あなたのナレッジが公開されました。",
     *                      "data": {
     *                      }
     *                 }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function publicKnowledge(PublicKnowledgeRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->message = __('knowledgeInput.public');
            $request->inform_contents =  __('knowledgeInput.inform_contents_public');
            $request->user_cd = $this->getCurrentUser();
            $status = $this->service->publicKnowledge($request);
            if (!$status) {
                return $this->responseErrorValidate(__('knowledgeInput.button_role', ["0" => "公開"]));
            }
            DB::commit();
            return $this->responseCreated(__('knowledgeInput.public'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('knowledgeInput.system_error'));
        }
    }

    /**
     * @OA\DELETE(
     *      path="/api/knowledge-input/delete-knowledge",
     *      operationId="deleteKnowledge",
     *      tags={"F01-S03"},
     *      summary="Knowledge Input",
     *      description="Delete Knowledge",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="knowledge_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="create_user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=204, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "204",
     *                     "message": "Deleted successfully.",
     *                     "data":{}
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */

    public function deleteKnowledge(DeleteKnowledgeRequest $request)
    {
        DB::beginTransaction();
        try {
            $knowledge_id = $request->knowledge_id;
            $create_user_cd = $request->create_user_cd;
            $user_cd = $this->getCurrentUser();
            $status = $this->service->deleteKnowledge($knowledge_id, $user_cd, $create_user_cd);

            if (!$status) {
                return $this->responseErrorValidate(__('knowledgeInput.button_role', ["0" => "削除"]));
            }

            DB::commit();
            return $this->responseNoContent(__('knowledgeInput.delete'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('knowledgeInput.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/knowledge-input/none-public",
     *      operationId="nonePublic",
     *      tags={"F01-S03"},
     *      summary="Knowledge Input",
     *      description="None Public",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Create User params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      description="knowledge_id",
     *                      default="1"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
     *                       "data": {}
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function nonePublic(NonePublicRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_cd = $this->getCurrentUser();
            $knowledge_id = $request->knowledge_id;
            $status = $this->service->nonePublic($user_cd,$knowledge_id,$request->approval_work_type,$request->approval_layer_num,$request);
            if (!$status) {
                return $this->responseErrorValidate(__('knowledgeInput.button_role', ["0" => "非公開"]));
            }
            DB::commit();
            return $this->responseCreated(__('knowledgeInput.save'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('knowledgeInput.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/knowledge-input/rejection",
     *      operationId="rejection",
     *      tags={"F01-S03"},
     *      summary="Knowledge Input",
     *      description="Rejection",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Create User params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      description="knowledge_id",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="submit_comment",
     *                      type="string",
     *                      description="submit_comment",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="create_user_cd",
     *                      type="string",
     *                      description="create_user_cd",
     *                      default="10002"
     *                  ),
     *                  @OA\Property(
     *                      property="mode_screen",
     *                      type="string",
     *                      description="mode_screen",
     *                      default="rejection"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="The knowledge source has been rejected.",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "ナレッジ元が不採用になりました。",
     *                       "data": {}
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function rejection(RejectionRequest $request)
    {
        DB::beginTransaction();
        try {
            $knowledge_id = $request->knowledge_id;
            $submit_comment = $request->submit_comment ?? '';
            $create_user_cd = $request->create_user_cd;
            $approval_work_type = $request->approval_work_type;
            $approval_layer_num = $request->approval_layer_num;
            $inform_contents = __('knowledgeInput.rejection');
            $user_cd = $this->getCurrentUser();
            $status = $this->service->rejection($knowledge_id, $submit_comment, $create_user_cd, $inform_contents, $user_cd,$approval_work_type,$approval_layer_num,$request);
            if (!$status) {
                return $this->responseErrorValidate(__('knowledgeInput.button_role', ["0" => "不採用"]));
            }
            DB::commit();
            return $this->responseCreated(__('knowledgeInput.rejection'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('knowledgeInput.system_error'));
        }
    }
}
