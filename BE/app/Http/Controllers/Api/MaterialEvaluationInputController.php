<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MaterialEvaluationInput\GetMaterialEvaluationInputRequest;
use App\Http\Requests\Api\LectureSeriesSelection\RegisterRequest;
use App\Http\Requests\Api\MaterialEvaluationInput\RegisterItemRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\MaterialEvaluationInputService;
use App\Services\AuthService;
use App\Services\SystemParameterService;

class MaterialEvaluationInputController extends Controller
{
    private $service, $auth, $system;

    public function __construct(MaterialEvaluationInputService $service, AuthService $auth, SystemParameterService $system)
    {
        $this->service = $service;
        $this->auth = $auth;
        $this->system = $system;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     *  @OA\GET(
     *      path="/api/material-evaluation-input",
     *      operationId="getMaterialEvaluationInput",
     *      tags={"D01-S06"},
     *      summary="Material Evaluation Input",
     *      description="Get Material Evaluation Input",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="document_usage_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="document_usage_id",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                           {
     *                               "document_id": 3,
     *                               "document_name": "test",
     *                               "usage_situation_id": 7,
     *                               "document_review": null,
     *                               "review_comment": null,
     *                               "source_document_id": null
     *                           },
     *                           {
     *                               "document_id": 2,
     *                               "document_name": "test",
     *                               "usage_situation_id": 9,
     *                               "document_review": null,
     *                               "review_comment": null,
     *                               "source_document_id": null
     *                           }
     *                       }
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getMaterialEvaluationInput(GetMaterialEvaluationInputRequest $request)
    {
        $document_usage_type = $request->document_usage_type;
        $document_usage_id = $request->document_usage_id;
        $datas = $this->service->getMaterialEvaluationInput($document_usage_type, $document_usage_id);
        return $this->responseSuccess('success', $datas);
    }


    /**
     *  @OA\POST(
     *  path="/api/material-evaluation-input/register-item",
     *  operationId="registerItem",
     *  tags={"D01-S06"},
     *  summary="Material Evaluation Input",
     *  description="Register Item",
     *  security={{"jwt": {}}},
     *  @OA\RequestBody(
     *      description="Register Item Params",
     *      required=true,
     *      @OA\MediaType(
     *          mediaType="application/json",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="data",
     *                  type="string",
     *                  description="data",
     *                  default="[{'document_id': 3,'usage_situation_id': 7,'document_review': 1,'review_comment': null,'review_customize_document_id': null,'review_datetime': null , 'source_document_id' : null}]"
     *              )
     *          ),
     *      ),
     *  ),
     *  @OA\Response(response=200, description="Response successfully",
     *           @OA\JsonContent(
     *            type="object",
     *            example={
     *                     "status": "200",
     *                     "message": "success",
     *                     "data": {
     *                         {
     *                             "document_id": 3,
     *                             "document_name": "test",
     *                             "usage_situation_id": 7,
     *                             "document_review": null,
     *                             "review_comment": null,
     *                             "source_document_id": null
     *                         },
     *                         {
     *                             "document_id": 2,
     *                             "document_name": "test",
     *                             "usage_situation_id": 9,
     *                             "document_review": null,
     *                             "review_comment": null,
     *                             "source_document_id": null
     *                         }
     *                     }
     *                 }
     *            )
     *      ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *  )
     */
    public function registerItem(RegisterItemRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->data;
            $user_cd =  $this->getCurrentUser();
            $data_user = $this->auth->getInfoUser($user_cd);
            $date_system = $this->system->getCurrentSystemDateTime();
            $receive_user_cd = $this->getCurrentUser();
            $this->service->registerItem($data, $date_system, $data_user,$receive_user_cd);
            DB::commit();
            return $this->responseCreated(__('materialevaluationinput.save'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('materialevaluationinput.system_error'));
        }
    }
}
