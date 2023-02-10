<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\OrganizationManagementService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrganizationManagemen\OrganizationManagemenRequest;
use App\Http\Requests\Api\OrganizationManagemen\OrganizationManagemenUserRequest;
class OrganizationManagementController extends Controller
{
    private $service;

    public function __construct(OrganizationManagementService $service)
    {
        $this->service = $service;
        $this->middleware('role.has:'
        .config('role.CALL_IMPLEMENTER.CODE').','
        .config('role.BRIEFING_IMPLEMENTER.CODE').','
        .config('role.CONVENTION_IMPLEMENTER.CODE').','
        .config('role.CONVENTION_ATTENDEE_MG.CODE').','
        .config('role.DOC_MG.CODE').','
        .config('role.QA_MG.CODE').','
        .config('role.KNOWLEDGE_MG.CODE').','
        .config('role.MESSAGE_MG.CODE').','
        .config('role.MASTER_MG.CODE').','
        .config('role.SYS_MG.CODE')
        );
    }

    /**
     * @OA\GET(
     *      path="/api/list-organization-management",
     *      operationId="listOrganizationlManagement",
     *      tags={"Z05-S07"},
     *      summary="Get list Organizationl Management",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="date",in="query", @OA\Schema(type="integer")),
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
    public function getListData(OrganizationManagemenRequest $request)
    {
        $data = $this->service->getListData($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/list-organization-management/user",
     *      operationId="listOrganizationManagementlUser",
     *      tags={"Z05-S07"},
     *      summary="Get list Organization Managementl User",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="date",in="query", @OA\Schema(type="integer")),
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
    public function getListUser(OrganizationManagemenUserRequest $request)
    {
        $data = $this->service->getListUser($request);
        return $this->responseSuccess('success', $data);
    }
}
