<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TargetSelectionPeriodManagement\SaveTargetSelectionPeriodManagementRequest;
use App\Services\TargetSelectionPeriodManagementService;
use App\Services\SystemParameterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class TargetSelectionPeriodManagementController extends Controller
{
    private $service;

    public function __construct(TargetSelectionPeriodManagementService $service, SystemParameterService $system)
    {
        $this->service = $service;
        $this->system = $system;
        $this->middleware('role.has:' . config('role.SYS_MG.CODE'));
    }

    /**
     *  @OA\GET(
     *    path="/api/target-selection-period-management",
     *    operationId="getTargetSelectionPeriodManagement",
     *    tags={"G01-S04"},
     *    summary="Target Selection Period Management",
     *    description="Get Target Selection Period Management",
     *    security={{"jwt": {}}},
     *    @OA\Response(response=200, description="Response successfully",
     *           @OA\JsonContent(
     *            type="object",
     *            example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                           {
     *                               "target_product_name": "ターゲットABC",
     *                               "target_product_cd": "ABC",
     *                               "selection_start_date": "2021-10-01",
     *                               "selection_end_date": "2021-12-31",
     *                               "start_date": "2021-10-01",
     *                               "end_date": "2021-12-31",
     *                           }
     *                       }
     *                   }
     *            )
     *      ),
     *    @OA\Response(
     *        response=403,
     *        description="Forbidden"
     *    )
     *  )
     */
    public function getTargetSelectionPeriodManagement()
    {
        $date_system = date_create($this->system->getCurrentSystemDateTime())->format('Y-m-d');
        $data = $this->service->getTargetSelectionPeriodManagement($date_system);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\POST(
     *      path="/api/target-selection-period-management/save",
     *      operationId="saveTargetSelectionPeriodManagement",
     *      tags={"G01-S04"},
     *      summary="Target Selection Period Management",
     *      description="Save Target Selection Period Management",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Save Target Selection Period Management params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="data",
     *                      type="string",
     *                      description="data",
     *                      default="['data' : [{ 'target_product_name': 'ターゲットABC', 'target_product_cd': 'ABC','selection_start_date': '2021-10-01','selection_end_date': '2021-12-31','start_date': '2019-04-01','end_date': '9999-12-31'}]]"
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
    public function saveTargetSelectionPeriodManagement(SaveTargetSelectionPeriodManagementRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->data ?? [];
            if(count($data) > 0){
                foreach($data as $item){
                    $item = (object)$item;
                    if(date_create($item->selection_start_date)->format('Y-m-d')  < date_create($item->start_date)->format('Y-m-d') ){
                        return $this->responseErrorValidate(__('targetselectionperiodmanagement.check_end_date' , [ 'attribute' => $item->start_date ]));
                    }elseif(date_create($item->selection_end_date)->format('Y-m-d')  > date_create($item->end_date)->format('Y-m-d')){
                        return $this->responseErrorValidate(__('targetselectionperiodmanagement.check_end_date' , [ 'attribute' => $item->end_date ]));
                    }elseif(date_create($item->selection_end_date)->format('Y-m-d')  <= date_create($item->selection_start_date)->format('Y-m-d')){
                        return $this->responseErrorValidate(__('targetselectionperiodmanagement.check_end_date_selection' , [ '0' => '選定期間開始日' , '1' => '選定期間終了日' ]));
                    }
                    
                    $this->service->saveTargetProduct($item->target_product_cd,$item->selection_start_date,$item->selection_end_date);
                }
            }
            DB::commit();
            return $this->responseCreated(__('targetselectionperiodmanagement.save'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('targetselectionperiodmanagement.system_error'));
        }
    }
}
