<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TimelineSearchService;
use App\Services\AuthService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Api\TimeLineSearch\TimeLineSearchRequest;

class TimelineSearchController extends Controller
{
    private $service;

    public function __construct(TimelineSearchService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/timeline-search",
     *      operationId="getIndexTimeline",
     *      tags={"F01-S04"},
     *      summary="get Index Timeline",
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
    public function getIndexTimeline()
    {
        $data = $this->service->getDataIndex();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/timeline-search/list-data",
     *      operationId="getListDataTimeline",
     *      tags={"F01-S04"},
     *      summary="get List Data Timeline",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="start_datetime",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="end_datetime",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="channel_type",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="facility_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="product_name",in="query", @OA\Schema(type="string")),
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
    public function getListDataTimeline(TimeLineSearchRequest $request)
    {
        $request->limit = $request->filled('limit') ? $request->limit : 100;
        $request->offset = $request->filled('offset') ? $request->offset : 0;
        $data = $this->service->getListDataTimeline($request);
        return $this->responseSuccess('success', $data);
    }
}
