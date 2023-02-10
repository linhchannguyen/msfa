<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Traits\ArrayTrait;
use Illuminate\Http\Request;
use App\Traits\DateTimeTrait;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Transformers\StockTransformer;
use App\Http\Requests\Api\Stock\AddStockRequest;
use App\Http\Requests\Api\Stock\EditStockRequest;
use App\Http\Requests\Api\Stock\DeleteStockRequest;

class StockController extends Controller
{
    use DateTimeTrait, ArrayTrait;
    private $service;

    public function __construct(StockService $service)
    {
        $this->service = $service;
        $this->middleware('role.has:' . config('role.CALL_IMPLEMENTER.CODE') . ',' . config('role.SYS_MG.CODE'));
    }

    /**
     * @OA\POST(
     *      path="/api/stock",
     *      operationId="searchStock",
     *      tags={"A02-S03"},
     *      summary="Search ttock",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="content_cd",
     *                      type="string",
     *                      description="活動内容",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="product_cd",
     *                      type="string",
     *                      description="品目",
     *                      default="[AAA-10]"
     *                  ),
     *                  @OA\Property(
     *                      property="status_type",
     *                      type="string",
     *                      description="ステータス",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="施設コード",
     *                      default="[011010002]"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_name",
     *                      type="string",
     *                      description="施設名",
     *                      default="一般社団法人田口医師会保健医療センター（札幌市中央区）"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="facility_category_type",
     *                      type="string",
     *                      description="施設分類",
     *                      default="01"
     *                  ),
     *                  @OA\Property(
     *                      property="person_name",
     *                      type="string",
     *                      description="医師名",
     *                      default="桜井 兼典"
     *                  ),
     *                  @OA\Property(
     *                      property="limit",
     *                      type="string",
     *                      description="limit",
     *                      default="100"
     *                  ),
     *                  @OA\Property(
     *                      property="offset",
     *                      type="string",
     *                      description="offset",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{{
     *                          "stock_id": 4,
     *                          "product_list": {
     *                              "product_cd": "BBB",
     *                              "product_name": "BBB製品"
     *                          },
     *                          "document_list": {{
     *                              "document_id": "2",
     *                              "document_name": "test",
     *                              "file_type": "1",
     *                              "document_type": "10"
     *                          },
     *                          {
     *                              "document_id": "3",
     *                              "document_name": "test",
     *                              "file_type": "1",
     *                              "document_type": "10"
     *                          }},
     *                          "facility_cd": "011010004",
     *                          "facility_category_type": "02",
     *                          "facility_name": "河野病院（札幌市中央区）",
     *                          "department_cd": "2351",
     *                          "department_name": "麻酔科・集中治療部",
     *                          "person_cd": "01101014",
     *                          "person_name": "関口 一樹",
     *                          "content_cd": "30",
     *                          "content_name": "説明会案内",
     *                          "status_type": "0",
     *                          "stock_type": "st1",
     *                          "action_id": "null",
     *                          "stock_date": "2021/11/12"
     *                     }}
     *                 }
     *              )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function searchStock(Request $request)
    {
        $data = [];
        $limit = $request->filled('limit') ? $request->limit : 100;
        $offset = $request->filled('offset') ? $request->offset : 0;
        $product_cd = $request->product_cd;
        $facility_cd = $request->facility_cd;
        $request->user_cd = $this->getCurrentUser();
        $request->product_cd = !is_array($product_cd) ? [] : $product_cd;
        $request->facility_cd = !is_array($facility_cd) ? [] : $facility_cd;
        $data = $this->service->search($request, $limit, $offset);
        if (!empty($request->facility_cd)) {
            $result['records'] = empty($data) ? [] : StockTransformer::collection($data);
            $result['pagination'] = [];
        } else {
            $result['records'] = empty($data['records']) ? [] : StockTransformer::collection($data['records']);
            $result['pagination'] = $data['pagination'];
        }
        return $this->responseSuccess('success', $result);
    }

    /**
     * @OA\POST(
     *      path="/api/stock/edit",
     *      operationId="updateStock",
     *      tags={"A02-S03"},
     *      summary="Edit stock",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="stock_id",
     *                      type="string",
     *                      description="stock id",
     *                      default="[2, 2]"
     *                  ),
     *                  @OA\Property(
     *                      property="content_cd",
     *                      type="string",
     *                      description="content cd",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="product_cd",
     *                      type="string",
     *                      description="product cd",
     *                      default="['AAA-10', 'BBB']"
     *                  ),
     *                  @OA\Property(
     *                      property="document_id",
     *                      type="string",
     *                      description="document id",
     *                      default="[2, 2]"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_category_type",
     *                      type="string",
     *                      description="facility category type",
     *                      default="[01, 02]"
     *                  ),
     *                  @OA\Property(
     *                      property="status_type",
     *                      type="string",
     *                      description="status type",
     *                      default="0"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function editStock(EditStockRequest $request)
    {
        DB::beginTransaction();
        try {
            $stock_ids = $request->stock_id;
            $result = false;
            $products = [];
            $documents = [];
            $product_cds = (!is_array($request->product_cd) || empty($request->product_cd)) ? [] : $request->product_cd;
            $document_ids = (!is_array($request->document_id) || empty($request->document_id)) ? [] : $request->document_id;
            $product_cds = array_unique($product_cds);
            $document_ids = array_unique($document_ids);
            $facility_category_type = (!is_array($request->facility_category_type) || empty($request->facility_category_type)) ? [] : $request->facility_category_type;
            if ($request->status_type === null || $request->status_type === "") { //If status_type !== null, this api is used to update stock's status_type
                if (empty($facility_category_type)) {
                    return $this->responseErrorValidate(__('stock.facility_category_type_error'));
                } else {
                    //Check facility_category_type, if the stock selected to update does not have the same facility_category_type, an error will be reported.
                    $arr_duplicate = [];
                    foreach ($facility_category_type as $value) {
                        if (!in_array($value, $arr_duplicate)) {
                            array_push($arr_duplicate, $value);
                        }
                    }
                    if (count($arr_duplicate) > 1) {
                        return $this->responseErrorValidate(__('stock.facility_category_type_error'));
                    }
                }
            }
            foreach ($stock_ids as $stock_id) {
                foreach ($product_cds as $product_cd) {
                    array_push($products, [
                        'stock_id' => $stock_id,
                        'product_cd' => $product_cd
                    ]);
                    break;
                }
                foreach ($document_ids as $document_id) {
                    array_push($documents, [
                        'stock_id' => $stock_id,
                        'document_id' => $document_id
                    ]);
                }
            }
            $request->products = $products;
            $request->documents = $documents;
            $result = $this->service->edit($request);

            DB::commit();
            return $this->responseCreated(__('stock.update_successfully'));
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            return $this->responseSystemError(__('stock.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/stock/create",
     *      operationId="addStock",
     *      tags={"A02-S01"},
     *      summary="Add stock",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="facility cd",
     *                      default="[011010014]"
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      description="person cd",
     *                      default="[01101156]"
     *                  ),
     *                  @OA\Property(
     *                      property="content_cd",
     *                      type="string",
     *                      description="content cd",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="product_cd",
     *                      type="string",
     *                      description="product cd",
     *                      default="['AAA-10', 'BBB']"
     *                  ),
     *                  @OA\Property(
     *                      property="stock_type",
     *                      type="string",
     *                      description="stock type",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="action_id",
     *                      type="string",
     *                      description="action id",
     *                      default=""
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function addStock(AddStockRequest $request)
    {
        DB::beginTransaction();
        try {
            $current = $this->currentJapanDateTime('Y-m-d');
            $stocks = [];
            $facility_cds = (!is_array($request->facility_cd) || empty($request->facility_cd)) ? [] : $request->facility_cd;
            $person_cds = (!is_array($request->person_cd) || empty($request->person_cd)) ? [] : $request->person_cd;
            $product_cds = (!is_array($request->product_cd) || empty($request->product_cd)) ? [] : $request->product_cd;
            $product_cds = array_unique($product_cds);
            foreach ($facility_cds as $key => $facility_cd) {
                $person_cd = $person_cds[$key] ?? "";
                //出席者管理からストックに登録できるのは、個人マスタに登録されている個人コードの行のみです。
                if (empty($person_cd)) {
                    $stocks = [];
                    break;
                }
                array_push($stocks, [
                    'facility_cd' => $facility_cd,
                    'person_cd' => $person_cd,
                    'content_cd' => $request->content_cd ?? null,
                    'status_type' => STOCK_STATUS_UNPLANNED,
                    'stock_type' => $request->stock_type ?? null,
                    'action_id' => $request->action_id ?? null,
                    'stock_date' => $current
                ]);
            }
            $request->stocks = $stocks;
            $request->product_cds = $product_cds;
            $this->service->create($request);
            DB::commit();
            return $this->responseCreated(__('stock.save_successfully'));
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('stock.system_error'));
        }
    }

    /**
     * @OA\DELETE(
     *      path="/api/stock",
     *      operationId="deleteStock",
     *      tags={"A02-S03"},
     *      summary="Delete stock",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="stock_id",
     *                      type="string",
     *                      description="stock id",
     *                      default="[2, 2]"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function deleteStock(DeleteStockRequest $request)
    {
        DB::beginTransaction();
        try {
            $stock_ids = !is_array($request->stock_id) ? [] : $request->stock_id;
            $stock_ids = $this->check_and_convert_to_array($stock_ids);
            $this->service->delete($request);
            DB::commit();
            return $this->responseSuccess(__('stock.delete_successfully'));
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('stock.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/stock/content",
     *      operationId="allContent",
     *      tags={"A02-S03"},
     *      summary="Get all content",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                          {
     *                             "inform_id": 2,
     *                             "inform_category_cd": "10",
     *                             "inform_category_name": "日報提出",
     *                             "inform_datetime": "2021-11-09 14:05:58",
     *                             "inform_user_cd": "10001",
     *                             "inform_contents": "Contenst",
     *                             "archive_flag": 0,
     *                             "informed_flag": 1
     *                          }
     *                     }
     *                 }
     *              )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function allContent()
    {
        $data = $this->service->contents();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/stock/facility-category",
     *      operationId="facilityCategoty",
     *      tags={"A02-S03"},
     *      summary="Get all facility category",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                          {
     *                              "facility_category_type": "01",
     *                              "facility_category_name": "大学病院"
     *                          },
     *                          {
     *                              "facility_category_type": "02",
     *                              "facility_category_name": "病院(200床以上)"
     *                          }
     *                     }
     *                 }
     *              )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function facilityCategoty()
    {
        $data = $this->service->facilityCategory();
        return $this->responseSuccess('success', $data);
    }
}
