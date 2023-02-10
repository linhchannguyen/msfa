<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LectureSeriesSelection\RegisterRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\LectureSeriesSelectionService;

class LectureSeriesSelectionController extends Controller
{
    private $service;

    public function __construct(LectureSeriesSelectionService $service)
    {
        $this->service = $service;
        $this->middleware('role.has:' . config('role.CONVENTION_IMPLEMENTER.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\GET(
     *      path="/api/lecture-series-selection",
     *      operationId="getLectureSeriesSelection",
     *      tags={"C01-S04"},
     *      summary="Lecture Series Selection",
     *      description="Get Lecture Series Selection",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="product_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="series_flag",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="org_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="limit",in="query", @OA\Schema(type="number")),
     *      @OA\Parameter(name="offset",in="query", @OA\Schema(type="number")),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                           "records": {
     *                               {
     *                                   "convention_id": 2,
     *                                   "convention_name": "test_2",
     *                                   "series_flag": 0,
     *                                   "series_convention_id" : ""
     *                               },
     *                               {
     *                                   "convention_id": 5,
     *                                   "convention_name": "test_5",
     *                                   "series_flag": 0,
     *                                   "series_convention_id" : ""
     *                               }
     *                           },
     *                           "pagination": {
     *                               "total_items": 7,
     *                               "total_pages": 1,
     *                               "previous_page": 1,
     *                               "next_page": 1,
     *                               "current_page": 1,
     *                               "first_page": 1,
     *                               "last_page": 1
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
    public function getLectureSeriesSelection(Request $request)
    {
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $convention_name = $request->convention_name;
        $series_flag = $request->series_flag;
        $product_cd = $request->product_cd;
        $datas = $this->service->getLectureSeriesSelection($product_cd, $convention_name, $series_flag, $limit, $offset);
        return $this->responseSuccess('success', $datas);
    }

    /**
     *  @OA\POST(
     *      path="/api/lecture-series-selection/register",
     *      operationId="register",
     *      tags={"C01-S04"},
     *      summary="Lecture Series Selection",
     *      description="Register",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Register params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="convention_id",
     *                      type="number",
     *                      description="Convention Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="parent_series_flag",
     *                      type="string",
     *                      description="parent_series_flag",
     *                      default=""
     *                  ),
     *                   @OA\Property(
     *                      property="series_convention_id",
     *                      type="string",
     *                      description="series_convention_id",
     *                      default=""
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Saved successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "201",
     *                       "message": "正常に保存しました。",
     *                       "data": {}
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $convention_id = $request->convention_id;
            $parent_series_flag = $request->parent_series_flag ?? 0;
            $series_convention_id = $request->series_convention_id ?? null;
            $this->service->register($convention_id,$parent_series_flag,$series_convention_id);
            DB::commit();
            return $this->responseCreated(__('lectureseriesselection.save'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('lectureseriesselection.system_error'));
        }
    }
}
