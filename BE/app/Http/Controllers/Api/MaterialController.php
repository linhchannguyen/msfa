<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\MaterialService;
use App\Http\Requests\Api\Material\MaterialFilterRequest;
use App\Http\Requests\Api\Material\MaterialFilterOrgRequest;
use App\Services\AuthService;

class MaterialController extends Controller
{
    private $service, $authService;

    public function __construct(MaterialService $service, AuthService $authService)
    {
        $this->service = $service;
        $this->authService = $authService;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/list-material",
     *      operationId="listMaterial",
     *      tags={"Z05-S05"},
     *      summary="Get list material",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "material": {
     *                          {
     *                              "document_type": "10",
     *                              "document_label": "施設"
     *                          }
     *                      },
     *                      "medical_subjects": {
     *                           {
     *                               "medical_subjects_group_cd": "1A",
     *                               "medical_subjects_group_name": "内科"
     *                           }
     *                      },
     *                      "safety":{
     *                          {
     *                              "safety_information_flag": "0",
     *                              "safety_information_label": "なし"
     *                          }
     *                      },
     *                      "off_label":{
     *                          {
     *                              "off_label_information_flag": "0",
     *                              "off_label_information_label": "なし"
     *                          }
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getData(Request $request)
    {
        $data = $this->service->getData($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/list-material/filter",
     *      operationId="listMaterialFilter",
     *      tags={"Z05-S05"},
     *      summary="Get list Material Filter",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="definition_value",in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="product_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="medical_subjects_group_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="safety",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="off_label",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                     {
     *                         "document_id": "",
     *                         "file_type": "",
     *                         "document_name": "",
     *                         "document_version": "",
     *                         "start_date": "",
     *                         "end_date": "",
     *                         "product_name": "",
     *                         "safety_information_flag": "",
     *                         "off_label_information_flag": ""
     *                     }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDataFilter(MaterialFilterRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getDataFilter($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/list-material/filter-org",
     *      operationId="listMaterialFilterOrg",
     *      tags={"Z05-S05"},
     *      summary="Get list Material Filter Org Document",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                         "list_org": "11110,11111,11112,10000,11000,11100",
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getOrgDocuemnt(MaterialFilterOrgRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getOrgDocuemnt($request);
        return $this->responseSuccess('success', $data);
    }
}
