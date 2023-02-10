<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InformCategoryService;

class InformCategoryController extends Controller
{
    private $service;

    public function __construct(InformCategoryService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/inform-category",
     *      operationId="getInformCategoryList",
     *      tags={"Z02-S03"},
     *      summary="Get inform category list",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      {
     *                          "inform_category_cd": "10",
     *                          "inform_category_name": "日報提出"
     *                      },
     *                      {
     *                          "inform_category_cd": "10",
     *                          "inform_category_name": "日報提出"
     *                      },
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getInformCategoryList()
    {
        $data = $this->service->all();
        return $this->responseSuccess('success', $data);
    }
}
