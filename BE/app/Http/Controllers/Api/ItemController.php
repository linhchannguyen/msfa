<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\ItemService;
use App\Services\SystemParameterService;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\Item\ItemFilterRequest;

class ItemController extends Controller
{
    private $service;
    private $system_service;

    public function __construct(ItemService $service, SystemParameterService $system_service)
    {
        $this->service = $service;
        $this->system_service = $system_service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/list-item",
     *      operationId="listitem",
     *      tags={"Z05-S06"},
     *      summary="Get list item",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      {
     *                          "definition_value": "10",
     *                          "definition_label": "全品目"
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getListItem()
    {
        $data = $this->service->GetListItem();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/list-item/filter",
     *      operationId="listitemfilter",
     *      tags={"Z05-S06"},
     *      summary="Get list item filter",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="definition_value",in="query", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="product_group_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      {
     *                          "product_cd": "AAA-10",
     *                          "product_group_cd": "AA",
     *                          "product_name": "AAA10mg",
     *                          "select_product": false
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDataFilter(ItemFilterRequest $request)
    {
        $time = $this->system_service->getCurrentSystemDateTime();
        $request->date = date('Y-m-d', strtotime($time));
        $data = $this->service->getDataFilter($request);
        return $this->responseSuccess('success', $data);
    }
}
