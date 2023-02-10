<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\RolePolicyService;
use App\Http\Controllers\Controller;
use App\Services\KnowledgeManagementService;
use App\Http\Requests\Api\KnowledgeManagement\DetailRequest;
use App\Http\Requests\Api\KnowledgeManagement\SearchRequest;
use App\Http\Requests\Api\KnowledgeManagement\SearchPreRequest;
use App\Http\Requests\Api\KnowledgeManagement\InsertCommentRequest;
use App\Http\Requests\Api\KnowledgeManagement\DeleteKnowledgeNiceRequest;
use App\Http\Requests\Api\KnowledgeManagement\InsertKnowledgeNiceRequest;
use App\Http\Requests\Api\KnowledgeManagement\InsertKnowledgeRequestRequest;

class KnowledgeManagementController extends Controller
{
    private $service, $role;

    public function __construct(KnowledgeManagementService $service, RolePolicyService $role)
    {
        $this->middleware('role.not:'.config('role.DEV.CODE'));
        $this->service = $service;
        $this->role = $role;
    }

    /**
     * @OA\GET(
     *      path="/api/knowledge-management/get-screen-data",
     *      operationId="getScreenDataKnowledgeManagement",
     *      tags={"F01-S01"},
     *      summary="Get screen data",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *          type="object",
     *          example={
     *                 "status":"success",
     *                 "data":{{
     *                      "knowledge_category": {{
     *                          "knowledge_category_name": "施策進捗",
     *                          "knowledge_category_cd": "10"
     *                      }},
     *                      "facility_type_group": {{
     *                          "facility_type_group_cd": "01",
     *                          "facility_type_group_name": "病院等"
     *                      }},
     *                      "knowledge_status": {{
     *                          "definition_value": "10",
     *                          "definition_label": "作成中"
     *                      }}
     *                 }}
     *             }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Error"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getScreenData()
    {
        $result = $this->service->getScreenData();
        return $this->responseSuccess('success', $result);
    }

    /**
     * @OA\POST(
     *      path="/api/knowledge-management/search",
     *      operationId="searchKnowledge",
     *      tags={"F01-S01"},
     *      summary="Search knowledge",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="knowledge_category_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="release_datetime_from",
     *                      type="string",
     *                      default="2021-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="release_datetime_to",
     *                      type="string",
     *                      default="2022-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="product_cd",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="facility_type_group_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="medical_subjects_group_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="prefecture_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="municipality_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="sort_by",
     *                      type="string",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="limit",
     *                      type="string",
     *                      default="100"
     *                  ),
     *                  @OA\Property(
     *                      property="offset",
     *                      type="string",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *          type="object",
     *          example={
     *                 "status":"success",
     *                 "data":{{
     *                      "records": {{
     *                          "knowledge_id": 1,
     *                          "knowledge_category_cd": "10",
     *                          "knowledge_category_name": "施策進捗",
     *                          "create_datetime": "2022/01/25 16:27:12",
     *                          "create_datetime_new": "14",
     *                          "last_update_datetime": "2022/01/25 16:27:12",
     *                          "last_update_datetime_updated": "7",
     *                          "title": "Know 1",
     *                          "contents": "Contents",
     *                          "product_name": "AA",
     *                          "medical_subjects_group_name": null,
     *                          "facility_type_group_name": null,
     *                          "facility_short_name": "田口医師会保健医療センター（札幌市中央区）",
     *                          "person_name": "和田 晴生",
     *                          "department_name": null,
     *                          "display_position_name": null,
     *                          "prefecture_name": "北海道",
     *                          "municipality_name": "札幌市中央区",
     *                          "create_user_cd": {{
     *                                  "org_label": "営業部門",
     *                                  "user_cd": "10002",
     *                                  "user_name": "安永 みづほ"
     *                          }},
     *                          "create_user_cd_together": {{
     *                                  "org_label": "営業部門",
     *                                  "user_cd": "10003",
     *                                  "user_name": "吉良 光浩"
     *                          },{
     *                                  "org_label": "北海道営業所",
     *                                  "user_cd": "10004",
     *                                  "user_name": "本郷 佐代"
     *                          }},
     *                          "knowledge_like": 1
     *                      }}
     *                 }}
     *             }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Error"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function searchKnowledge(SearchRequest $request)
    {
        try {
            $user_cd = $this->getCurrentUser();
            $request->create_user_cd = $user_cd;
            $roles = $this->role->getRoleList($user_cd);
            $request->roles = $roles;
            $request->limit = $request->filled('limit') ? $request->limit : 100;
            $request->offset = $request->filled('offset') ? $request->offset : 0;
            $result = $this->service->search($request);
            return $this->responseSuccess('success', $result);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('knowledge.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/knowledge-management/knowledge-detail/{knowledge_id}",
     *      operationId="getKnowledgeDetail",
     *      tags={"F01-S02"},
     *      summary="Get knowledge detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="limit",
     *                      type="string",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="offset",
     *                      type="string",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *          type="object",
     *          example={
     *                 "status":"success",
     *                 "data":{{
     *                     "knowledge_id": 1,
     *                     "title": "Know 1",
     *                     "knowledge_category_cd": "10",
     *                     "knowledge_category_name": "施策進捗",
     *                     "release_datetime": "2022/01/25",
     *                     "anonymous_flag": 1,
     *                     "product_name": "匿名",
     *                     "facility_short_name": "匿名",
     *                     "department_name": null,
     *                     "person_name": "和田 晴生",
     *                     "display_position_name": null,
     *                     "facility_type_group_name": null,
     *                     "medical_subjects_group_name": null,
     *                     "prefecture_name": "北海道",
     *                     "municipality_name": "札幌市中央区",
     *                     "contents": "",
     *                     "create_user_cd": "10002",
     *                     "create_user_name": "匿名",
     *                     "create_user_cd_together": "",
     *                     "approval_user_cd": {},
     *                     "timelines": {},
     *                     "evaluation_comments": {},
     *                     "user_approval": {},
     *                     "total_evaluation_comment": 6,
     *                     "total_knowledge_nice": 7
     *                 }}
     *             }
     *          )
     *      )
     * )
     */
    public function getKnowledgeDetail($knowledge_id, DetailRequest $request)
    {
        $conditions = new stdClass;
        $user_cd = $this->getCurrentUser();
        $roles = $this->role->getRoleList($user_cd);
        $conditions->roles = $roles;
        $conditions->create_user_cd = $user_cd;
        $conditions->knowledge_id = $knowledge_id;
        $conditions->limit = $request->filled('limit') ? $request->limit : 10;
        $conditions->offset = $request->filled('offset') ? $request->offset : 0;
        $data = $this->service->knowledgeDetail($conditions);
        if (!$data) {
            return $this->responseErrorValidate(__('knowledge.data_not_found'));
        }
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\POST(
     *      path="/api/knowledge-management/register-knowledge-nice",
     *      operationId="registerKnowledgeNice",
     *      tags={"F01-S02"},
     *      summary="Register Knowledge Nice",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="create_user_cd",
     *                      type="string",
     *                      default="10002"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function registerKnowledgeNice(InsertKnowledgeNiceRequest $request)
    {
        DB::beginTransaction();
        $user_cd = $this->getCurrentUser();
        $request->user_login = $user_cd;
        $result = $this->service->createKnowledgeNice($request);
        if (!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('knowledge.system_error'));
        }
        DB::commit();
        return $this->responseCreated(__('knowledge.create_successfully'));
    }

    /**
     * @OA\POST(
     *      path="/api/knowledge-management/register-comment",
     *      operationId="registerComment",
     *      tags={"F01-S02"},
     *      summary="Register Comment",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="comment",
     *                      type="string",
     *                      default="comment"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function registerComment(InsertCommentRequest $request)
    {
        DB::beginTransaction();
        $user_cd = $this->getCurrentUser();
        $request->user_login = $user_cd;
        $result = $this->service->upsertComment($request);
        if (!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('knowledge.system_error'));
        }
        DB::commit();
        return $this->responseCreated(__('knowledge.create_successfully'));
    }

    /**
     * @OA\PUT(
     *      path="/api/knowledge-management/delete-knowledge-nice",
     *      operationId="deleteKnowledgeNice",
     *      tags={"F01-S02"},
     *      summary="Delete Knowledge Nice",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="nice_id",
     *                      type="string",
     *                      default="1"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Error"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function deleteKnowledgeNice(DeleteKnowledgeNiceRequest $request)
    {
        /*
        * ◆ナレッジ管理者・最終承認者の場合
        * 　非表示
        * ◆上記以外
        * 　表示
        */
        $knowledge = $this->service->getKnowledgeNiceById($request->nice_id);
        $knowledge_id = $knowledge->knowledge_id ?? "";
        if ($knowledge_id == "") {
            return $this->responseErrorValidate(__('knowledge.data_not_found'));
        }
        $this->checkActionDeleteKnowledgeNice($knowledge_id);
        DB::beginTransaction();
        $user_cd = $this->getCurrentUser();
        $request->user_login = $user_cd;
        $result = $this->service->deleteKnowledgeNice($request);
        if (!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('knowledge.system_error'));
        }
        DB::commit();
        return $this->responseCreated(__('knowledge.delete_successfully'));
    }

    /**
     * @OA\POST(
     *      path="/api/knowledge-management/register-knowledge-request",
     *      operationId="registerKnowledgeRequest",
     *      tags={"F01-S02"},
     *      summary="Register Knowledge Request",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="demand_note",
     *                      type="string",
     *                      default="comment"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function registerKnowledgeRequest(InsertKnowledgeRequestRequest $request)
    {
        /*
        * ◆ナレッジ管理者・最終承認者の場合
        * 　非表示
        * ◆上記以外
        * 　表示
        */
        $this->checkActionRegisterKnowledgeRequest($request->knowledge_id);
        DB::beginTransaction();
        $user_cd = $this->getCurrentUser();
        $request->user_login = $user_cd;
        $result = $this->service->registerKnowledgeRequest($request);
        if (!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('knowledge.system_error'));
        }
        DB::commit();
        return $this->responseCreated(__('knowledge.create_successfully'));
    }

    public function checkActionDeleteKnowledgeNice($knowledge_id)
    {
        $user_cd = $this->getCurrentUser();
        $user_info = $this->getInfoCurrentUser($user_cd, IS_USER);
        $roles = $this->role->getRoleList($user_cd);
        $knowledge = $this->service->getKnowledge($knowledge_id);
        $check = false;
        $approval_user_cd = empty($knowledge[0]->approval_user_cd) ? [] : json_decode($knowledge[0]->approval_user_cd, true);
        if (in_array(config('role.KNOWLEDGE_MG.CODE'), $roles)) {
            $check = true;
        }
        foreach ($approval_user_cd as $approval_user) {
            if ($approval_user['approval_user_cd'] == $user_cd) {
                $check = true;
                break;
            }
        }
        if (!$check) {
            abort($this->responseErrorForbidden(__('knowledge.access_denied', ['attribute' => $user_info->user_name ?? ""])));
        }
    }

    public function checkActionRegisterKnowledgeRequest($knowledge_id)
    {
        $user_cd = $this->getCurrentUser();
        $user_info = $this->getInfoCurrentUser($user_cd, IS_USER);
        $roles = $this->role->getRoleList($user_cd);
        $knowledge = $this->service->getKnowledge($knowledge_id);
        $approval_user_cd = empty($knowledge[0]->approval_user_cd) ? [] : json_decode($knowledge[0]->approval_user_cd, true);
        if (in_array(config('role.KNOWLEDGE_MG.CODE'), $roles)) {
            abort($this->responseErrorForbidden(__('knowledge.access_denied', ['attribute' => $user_info->user_name ?? ""])));
        }
        foreach ($approval_user_cd as $approval_user) {
            if ($approval_user['approval_user_cd'] == $user_cd) {
                abort($this->responseErrorForbidden(__('knowledge.access_denied', ['attribute' => $user_info->user_name ?? ""])));
                break;
            }
        }
    }

    /**
     * @OA\POST(
     *      path="/api/knowledge-management/search-pre",
     *      operationId="searchPre",
     *      tags={"F01-S05"},
     *      summary="Search knowledge pre public",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="knowledge_category_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="knowledge_id",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="release_datetime_from",
     *                      type="string",
     *                      default="2021-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="release_datetime_to",
     *                      type="string",
     *                      default="2022-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="product_cd",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="facility_type_group_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="medical_subjects_group_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="prefecture_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="municipality_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="knowledge_status_type",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="limit",
     *                      type="string",
     *                      default="100"
     *                  ),
     *                  @OA\Property(
     *                      property="offset",
     *                      type="string",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *          type="object",
     *          example={
     *                 "status":"success",
     *                 "data":{{
     *                      "records": {{
     *                          "knowledge_id": 516,
     *                          "anonymous_flag": 0,
     *                          "knowledge_category_cd": "100",
     *                          "knowledge_status_type": "10",
     *                          "definition_label": "回答受付中",
     *                          "knowledge_category_name": "その他",
     *                          "create_datetime": "2022/02/22 00:00:00",
     *                          "create_datetime_new": "14",
     *                          "last_update_datetime": "2022/02/22 00:00:00",
     *                          "last_update_datetime_updated": "7",
     *                          "title": "",
     *                          "contents": "",
     *                          "product_name": null,
     *                          "medical_subjects_group_name": null,
     *                          "facility_type_group_name": "診療所",
     *                          "facility_short_name": "秋田医院　医療（札幌市中央区）",
     *                          "person_name": "沢田 嘉邦",
     *                          "department_name": null,
     *                          "display_position_name": null,
     *                          "prefecture_name": "北海道",
     *                          "municipality_name": "札幌市中央区",
     *                          "create_user_cd": "東北北営業所 弓削 悟志",
     *                          "create_user_cd_together": "営業部門 西嶋 洋明",
     *                          "approval_user_cd": "北海道営業所 本郷 佐代",
     *                          "knowledge_like": 0
     *                      }}
     *                 }}
     *             }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function searchPre(SearchPreRequest $request)
    {
        $user_cd = $this->getCurrentUser();
        $roles = $this->role->getRoleList($user_cd);
        $request->roles = $roles;
        $request->create_user_cd = $user_cd;
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $result = $this->service->searchPre($request);
        return $this->responseSuccess('success', $result);
    }
}
