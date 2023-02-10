<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\OrganizationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Organization\OrganizationUserRequest;
use App\Traits\Base64StringFileTrait;

class OrganizationController extends Controller
{
    use Base64StringFileTrait;
    private $service;

    public function __construct(OrganizationService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/list-organization",
     *      operationId="listOrganizationl",
     *      tags={"Z05-S01"},
     *      summary="Get list Material Filter",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "title":{
     *                           {
     *                               "definition_value": "1",
     *                               "definition_label": "部門"
     *                           }
     *                      },
     *                      "org_obj" : {
     *                           "org_cd": "10000",
     *                           "org_name": "営業部門",
     *                           "layer_num": 1,
     *                           "org_label": "営業部門",
     *                           "children" : {}
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getListData()
    {
        $data = $this->service->getListData();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/list-organization/user",
     *      operationId="listOrganizationlUser",
     *      tags={"Z05-S01"},
     *      summary="Get list Material User",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                     {
     *                         "org_cd": "10000",
     *                         "org_name": "営業部門",
     *                         "layer_num": 1,
     *                         "user_cd": "10001",
     *                         "user_name": "西嶋 洋明",
     *                         "main_flag": 1
     *                     }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getListUser(OrganizationUserRequest $request)
    {
        $data = $this->service->getListUser($request);
        foreach($data as $value){
            $value->file_url = $this->encodeBase64String($value->file_url);
        }
        return $this->responseSuccess('success', $data);
    }
}
