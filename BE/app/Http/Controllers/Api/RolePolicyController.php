<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Role\GetRoleRequest;
use App\Services\RolePolicyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolePolicyController extends Controller
{
    private $service;

    public function __construct(RolePolicyService $service)
    {
        $this->service = $service;
    }


    /**
     *  @OA\GET(
     *      path="/api/policy/role",
     *      operationId="getRoleList",
     *      tags={"policy"},
     *      summary="Get role list of current logged user",
     *      description="Get role list of current logged user",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                      "status": "200",
     *                      "message": "success",
     *                      "data": {
     *                          "R01",
     *                          "R10",
     *                          "R20",
     *                          "R30"
     *                      }
     *                  }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getRoleList(GetRoleRequest $request)
    {
        $user_cd = $request->user_cd ?? $this->getCurrentUser();
        $result = $this->service->getRoleList($user_cd);
        return $this->responseSuccess("success", $result);
    }

    /**
     *  @OA\GET(
     *      path="/api/policy/permission",
     *      operationId="getPermissionList",
     *      tags={"policy"},
     *      summary="Get permission list of current logged user",
     *      description="Get permission list of current logged user",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object"
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getPermissionList(GetRoleRequest $request)
    {
        $user_cd = $request->user_cd ?? $this->getCurrentUser();
        $result = $this->service->getPermissionList($user_cd);
        return $this->responseSuccess("success", $result);
    }
}
