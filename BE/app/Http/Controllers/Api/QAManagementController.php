<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\RolePolicyService;
use App\Http\Controllers\Controller;
use App\Services\QAManagementService;
use App\Http\Requests\Api\QAManagement\UpsertRequest;
use App\Http\Requests\Api\QAManagement\SearchQARequest;

class QAManagementController extends Controller
{
    private $service, $role;

    public function __construct(QAManagementService $service, RolePolicyService $role)
    {
        $this->middleware('role.not:'.config('role.DEV.CODE'));
        $this->service = $service;
        $this->role = $role;
    }

    /**
     * @OA\GET(
     *      path="/api/qa-management/get-screen-data",
     *      operationId="getScreenDataQaManagement",
     *      tags={"E01-S03"},
     *      summary="Get screen data",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *          type="object",
     *          example={
     *                 "status":"success",
     *                 "data":{{
     *                      "question_status": {{
     *                          "qa_category_cd": "10",
     *                          "qa_category_name": "自社製品"
     *                      }},
     *                      "question_category": {{
     *                          "qa_category_cd": "10",
     *                          "qa_category_name": "自社製品"
     *                      }},
     *                      "posting_prohibited": 1
     *                 }}
     *             }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getScreenData()
    {
        $user_cd =  $this->getCurrentUser();
        $result = $this->service->getScreenData($user_cd);
        return $this->responseSuccess('success', $result);
    }

    /**
     * @OA\POST(
     *      path="/api/qa-management/create-qa",
     *      operationId="createtQA",
     *      tags={"E01-S03"},
     *      summary="Create QA",
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
     *                      property="question_category_cd",
     *                      type="string",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      default="title"
     *                  ),
     *                  @OA\Property(
     *                      property="contents",
     *                      type="string",
     *                      default="contents"
     *                  ),
     *                  @OA\Property(
     *                      property="org_cd",
     *                      type="string",
     *                      default="[11000, 12000]"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function createtQA(UpsertRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->user_cd = $this->getCurrentUser();
            //Q&A 200
            $posting_prohibited = $this->service->checkUserUnsuitableReport($this->getCurrentUser());
            if($posting_prohibited){
                return $this->responseErrorForbidden(__('qa.access_denied'));
            }
            //Q&A 200
            $this->service->createtQA($request);
            DB::commit();
            return $this->responseCreated(__('qa.create_successfully'));
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('qa.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/qa-management/search",
     *      operationId="searchQA",
     *      tags={"E01-S01"},
     *      summary="Search QA",
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
     *                      property="question_category_cd",
     *                      type="string",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="question_status_type",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="question_hot",
     *                      type="string",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="new_arrival",
     *                      type="string",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="contents",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="unsuitable_report",
     *                      type="string",
     *                      default="0"
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
     *                     "records": {{
     *                         "question_id": 1,
     *                         "question_category_cd": "10",
     *                         "question_status_type": "10",
     *                         "definition_label": "回答受付中",
     *                         "create_user_cd": "10002",
     *                         "title": "10",
     *                         "contents": "10221",
     *                         "last_update_datetime": "2022-01-28 10:51:56",
     *                         "delete_flag": 0,
     *                         "qa_hot": 2,
     *                         "new_arrival": 0,
     *                         "quantity_answer": 2,
     *                         "answer_note": "test 1"
     *                     }},
     *                 }}
     *             }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function searchQA(SearchQARequest $request)
    {
        try {
            $user_cd = $this->getCurrentUser();
            $roles = $this->role->getRoleList($user_cd);
            $request->roles = $roles;
            $request->limit = $request->filled('limit') ? $request->limit : 100;
            $request->offset = $request->filled('offset') ? $request->offset : 0;
            $data = $this->service->searchQA($request);
            return $this->responseSuccess('success', $data);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('qa.system_error'));
        }
    }
}
