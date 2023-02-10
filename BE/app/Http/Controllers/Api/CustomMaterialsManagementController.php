<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\CustomMaterialManagementService;
use App\Http\Requests\Api\CustomMaterialManagement\CopyCustomMaterialRequest;
use App\Http\Requests\Api\CustomMaterialManagement\CustomMaterialManagementRequest;

class CustomMaterialsManagementController extends Controller
{
    private $service;

    public function __construct(CustomMaterialManagementService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/custom-material-management/get-screen-data",
     *      operationId="getScreenDataCustomMaterial",
     *      tags={"D01-S04"},
     *      summary="Get Screen Data",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "medical_subjects_group":{
     *                          {
     *                              "medical_subjects_group_cd": "1A",
     *                              "medical_subjects_group_name": "内科"
     *                          }
     *                      },
     *                      "variable_definition":{
     *                          {
     *                              "definition_value": "0",
     *                              "definition_label": "なし"
     *                          }
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function getScreenDataCustomMaterial()
    {
        $data = $this->service->getScreenData();
        return $this->responseSuccess("success", $data);
    }

    /**
     * @OA\POST(
     *      path="/api/custom-material-management/search",
     *      operationId="searchCustomMaterial",
     *      tags={"D01-S04"},
     *      summary="Search custom-material",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="disuse_flag",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="document_name",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="product_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="disease",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="medical_subjects_group_cd",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="applicable_date",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="safety_information_flag",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="off_label_information_flag",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="offset",
     *                      type="string",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="limit",
     *                      type="string",
     *                      default="100"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                         "records": {
     *                             {
     *                                 "document_id": 3,
     *                                 "document_type": "10",
     *                                 "document_name": "test",
     *                                 "start_date": "2021-10-01",
     *                                 "end_date": "9999-12-31",
     *                                 "document_contents": "test",
     *                                 "disease": "対象疾患",
     *                                 "medical_subjects_group_cd": "1A",
     *                                 "medical_subjects_group_name": "内科",
     *                                 "safety_information_flag": 1,
     *                                 "off_label_information_flag": 1,
     *                                 "create_user_cd": "1",
     *                                 "last_update_datetime": "2021-11-08 10:33:03",
     *                                 "file_type": "1",
     *                                 "product_cd": "AAA-10",
     *                                 "product_name": "AAA10mg",
     *                                 "definition_label_a8": "あり",
     *                                 "definition_label_a9": "あり",
     *                                 "edit_mode": 0
     *                             }
     *                         }
     *                     }
     *                 }
     *              )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function searchCustomMaterial(CustomMaterialManagementRequest $request)
    {
        try {
            $request->limit = $request->filled('limit') ? $request->limit : 100;
            $request->offset = $request->filled('offset') ? $request->offset : 0;
            $request->product_cd = empty($request->product_cd) ? [] : $request->product_cd;
            $request->disuse_flag = $request->disuse_flag . "";
            $request->user_cd = $this->getCurrentUser();
            $data = $this->service->searchCustomMaterial($request);
            return $this->responseSuccess('success', $data);
        } catch (Exception $ex) {
            throw $ex;
            return $this->responseSystemError(__('custom_material_management.system_error'));
        }
    }
}
