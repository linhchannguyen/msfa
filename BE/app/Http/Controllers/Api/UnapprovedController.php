<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\UnapprovedService;
use App\Http\Controllers\Controller;

class UnapprovedController extends Controller
{
    private $service;

    public function __construct(UnapprovedService $service)
    {
        $this->service = $service;
        $this->middleware('role.has:'.config('role.CALL_IMPLEMENTER.CODE').','.config('role.SYS_MG.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/unapproved-list",
     *      operationId="listUnapproved",
     *      tags={"A03-S03"},
     *      summary="Get list Unapproved",
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
    public function getData(Request $request)
    {
        $user_cd =  $this->getCurrentUser();
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $request->user_cd = $user_cd;
        $data = $this->service->getData($request);
        return $this->responseSuccess('success', $data);
    }
}
