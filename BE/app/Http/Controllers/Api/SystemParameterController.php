<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GetSystemParameterService;

class SystemParameterController extends Controller
{
    public function __construct(GetSystemParameterService $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @OA\GET(
     *      path="/api/get-system-parameter",
     *      operationId="getSystemParameter",
     *      summary="Get System Parameter",
     *      description="",
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                           "system_name": {
     *                               "parameter_name": "システム全体設定",
     *                               "parameter_key": "システム名",
     *                               "parameter_value": "Co-ReFit",
     *                               "remarks": "システムの名称"
     *                           }
     *                       }
     *                   }
     *          )
     *      )
     * )
     */
    public function getSystemParameter()
    {
        $data = $this->repo->getSystemParameter();
        return $this->responseSuccess("success", $data);
    }
}
