<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\InterviewContentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InterviewContent\InterviewContentRequest;

class InterviewContentController extends Controller
{
    private $service;

    public function __construct(InterviewContentService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/interview-content-setting",
     *      operationId="InterviewContentSetting",
     *      tags={"Z03-S03"},
     *      summary="Interview Content Setting",
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
    public function getData()
    {
        $data = $this->service->getData();
        return $this->responseSuccess('success', $data);
    }
}
