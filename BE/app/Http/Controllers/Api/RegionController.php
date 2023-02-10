<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\RegionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Region\RegionPrefectureRequest;
use App\Http\Requests\Api\Region\RegionPrefectureMunicipalityRequest;

class RegionController extends Controller
{
    private $service;

    public function __construct(RegionService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/list-region",
     *      operationId="listRegion",
     *      tags={"Z05-S02"},
     *      summary="Get list Organization Managementl Filter",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                     {
     *                         "region_cd": "1",
     *                         "region_name": "北海道・東北",
     *                     }
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
     *      path="/api/list-region/prefecture",
     *      operationId="listRegionPrefecture",
     *      tags={"Z05-S02"},
     *      summary="Get list Organization Managementl Filter",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="region_cd",in="query", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                     {
     *                         "prefecture_cd": "01",
     *                         "prefecture_name": "北海道",
     *                     }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDataPrefecture(RegionPrefectureRequest $request)
    {
        $data = $this->service->getDataPrefecture($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/list-region/prefecture/municipality",
     *      operationId="listRegionPrefectureMunicipality",
     *      tags={"Z05-S02"},
     *      summary="Get list Organization Managementl Filter",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="prefecture_cd",in="query", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                     {
     *                         "municipality_cd": "101",
     *                         "municipality_name": "札幌市中央区",
     *                         "municipality_name_kana": "札幌市中央区",
     *                     }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDataMunicipality(RegionPrefectureMunicipalityRequest $request)
    {
        $data = $this->service->getDataMunicipality($request);
        return $this->responseSuccess('success', $data);
    }
}
