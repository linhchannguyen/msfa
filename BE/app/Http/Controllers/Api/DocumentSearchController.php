<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\DocumentSearchService;
use App\Http\Requests\Api\DocumentSearch\DocumentAllRequest;
use App\Http\Requests\Api\DocumentSearch\DocumentProfileRequest;
use App\Http\Requests\Api\DocumentSearch\DocumentReviewCommentRequest;
use App\Http\Requests\Api\DocumentSearch\SaveDocumentRequest;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AuthService;

use function PHPUnit\Framework\returnSelf;

class DocumentSearchController extends Controller
{
    private $service, $authService;

    public function __construct(
        DocumentSearchService $service,
        AuthService $authService
    ) {
        $this->service = $service;
        $this->authService = $authService;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/document-search",
     *      operationId="GetDataIndexDocumentSearch",
     *      tags={"D01-S01"},
     *      summary="Get data index document search",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "safety_information": {
     *                          {
     *                              "safety_value": "",
     *                              "safety_label": "全て"
     *                          },
     *                          {
     *                              "safety_value": "0",
     *                              "safety_label": "なし"
     *                          },
     *                          {
     *                              "safety_value": "1",
     *                              "safety_label": "あり"
     *                          }
     *                      },
     *                      "not_applicable_information": {
     *                          {
     *                              "not_applicable_value": "",
     *                              "not_applicable_label": "全て"
     *                          },
     *                          {
     *                              "not_applicable_value": "0",
     *                              "not_applicable_label": "なし"
     *                          },
     *                          {
     *                              "not_applicable_value": "1",
     *                              "not_applicable_label": "あり"
     *                          }
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function index()
    {
        $data = $this->service->getData();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/document-search/list",
     *      operationId="GetDataDocument",
     *      tags={"D01-S01"},
     *      summary="Get list Document",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="document_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="product_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="disease",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="medical_subjects_group_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="safety_information_flag",in="query", @OA\Schema(type="int")),
     *      @OA\Parameter(name="off_label_information_flag",in="query", @OA\Schema(type="int")),
     *      @OA\Parameter(name="date",in="query", @OA\Schema(type="int")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "records": {
     *                          {
     *                              "last_update_datetime": "2021-11-08 10:33:03",
     *                              "file_type": "1",
     *                              "document_name": "test",
     *                              "disease": "対象疾患",
     *                              "document_version": "2.1",
     *                              "product_cd": "AAA-10",
     *                              "product_name": "AAA10mg",
     *                              "medical_subjects_group_cd": "1A",
     *                              "medical_subjects_group_name": "内科",
     *                              "safety_information_flag": 1,
     *                              "safety_information_label": "あり",
     *                              "off_label_information_flag": 1,
     *                              "off_label_information_label": "あり",
     *                              "review_comment": 0,
     *                              "document_review": "0.00",
     *                              "start_date": "2021-10-01",
     *                              "end_date": "2021-12-01"
     *                          }
     *                      },
     *                      "pagination": {
     *                          "total_items": 1,
     *                          "total_pages": 1,
     *                          "previous_page": 1,
     *                          "next_page": 1,
     *                          "current_page": 1,
     *                          "first_page": 1,
     *                          "last_page": 1
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getData(DocumentAllRequest $request)
    {
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $request->user_login = $this->getCurrentUser();
        $user_detail = $this->authService->getInfoUser($request->user_login);
        $request->org_user = $user_detail->org_cd ?? "";
        $data = $this->service->allData($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/document-search/detail",
     *      operationId="GetDataDocumentDetail",
     *      tags={"D01-S02"},
     *      summary="Get Document Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="document_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDocumentDetail(DocumentProfileRequest $request)
    {
        $data = $this->service->getDocumentDetail($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/document-search/detail/profile",
     *      operationId="GetDataDocumentProfile",
     *      tags={"D01-S02"},
     *      summary="Get Document Profile",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="document_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDocumentProfile(DocumentProfileRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getDocumentProfile($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/document-search/detail/review-comment",
     *      operationId="getDocumentReviewComment",
     *      tags={"D01-S02"},
     *      summary="Get Document Review Comment",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="document_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDocumentReviewComment(DocumentProfileRequest $request)
    {
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $data = $this->service->getDocumentReviewComment($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/document-search/detail/custom",
     *      operationId="getDocumentCustom",
     *      tags={"D01-S02"},
     *      summary="Get Document Custom",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="document_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDocumentCustom(DocumentProfileRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $data = $this->service->getDocumentCustom($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/document-search/registration",
     *      operationId="GetDataDocumentRegistrationIndex",
     *      tags={"D01-S03"},
     *      summary="Get index Document Registration",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function indexDocumentRegistration()
    {
        $data = $this->service->indexDocumentRegistration();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/document-search/registration/detail",
     *      operationId="GetDataDocumentRegistrationDetail",
     *      tags={"D01-S03"},
     *      summary="Get Document Registration Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="document_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDocumentRegistrationDetail(DocumentProfileRequest $request)
    {
        $data = $this->service->getDocumentRegistrationDetail($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\PUT(
     *      path="/api/document-search/registration/save",
     *      operationId="SaveDataDocumentRegistration",
     *      tags={"D01-S03"},
     *      summary="Save Document Registration Detail",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="document_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="document_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="document_contents",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="document_version",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="parent_document_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="end_date",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="available_org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="org_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="product_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="disease",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="medical_subjects_group_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="management_org_name",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="document_category_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="safety_information_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="off_label_information_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="file_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="document_type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=201, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function saveDocumentRegistrationDetail(SaveDocumentRequest $request)
    {
        DB::beginTransaction();
        try {
            set_time_limit(0);
            if ($request->document_file) {
                if ($request->document_file->getClientOriginalExtension() === 'mp4' && $request->document_category_cd == 90) {
                    return $this->responseErrorValidate('画像の資材では資材種別を表紙・目次として使用できません。');
                }
            }
            $request->user_login = $this->getCurrentUser();
            $request->management_org_cd = $this->authService->getInfoUser($request->user_login)->org_cd;
            $request->management_org_name = $this->authService->getInfoUser($request->user_login)->org_short_name;
            $data = $this->service->saveDocumentRegistrationDetail($request);
            DB::commit();
            return $this->responseCreated(__('usermanagement.save_successfully'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('usermanagement.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/document-search/registration/delete",
     *      operationId="deleteDocumentRegistrationDetail",
     *      tags={"D01-S03"},
     *      summary="Get Document Delete",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="document_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function deleteDocumentRegistrationDetail(DocumentProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $document_usage = $this->service->getDocumentUsageSituation($request);
            $document_composition = $this->service->getDocumentComposition($request);
            if (isset($document_usage->usage_situation_id) || isset($document_composition->document_id)) {
                return $this->responseErrorForbidden('資材がユーザに使用されているため削除できません。資材登録から資材の改訂を行ってください。');
            }
            $this->service->deleteDocument($request);
            DB::commit();
            return $this->responseNoContent('success');
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('usermanagement.system_error'));
        }
    }
}
