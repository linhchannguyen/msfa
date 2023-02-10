<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\RolePolicyService;
use App\Http\Controllers\Controller;
use App\Services\OrganizationService;
use App\Services\AttendantManagementService;
use App\Http\Requests\Api\AttendantManagement\AddAttendantRequest;
use App\Http\Requests\Api\AttendantManagement\SearchAttendantRequest;

class AttendantManagementController extends Controller
{
    private $service, $role, $organization_service;

    public function __construct(AttendantManagementService $service, RolePolicyService $role, OrganizationService $organization_service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
        $this->role = $role;
        $this->organization_service = $organization_service;
    }

    /**
     * @OA\GET(
     *      path="/api/attendant-management/get-screen-data",
     *      operationId="getScreenDataAttendant",
     *      tags={"C01-S03"},
     *      summary="Get screen data",
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
     *                      property="convention_id",
     *                      type="string",
     *                      default="2"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *          type="object",
     *          example={
     *                 "status":"success",
     *                 "data":{{
     *                      "convention_info": {{
     *                          "definition_label": "計画入力中",
     *                          "convention_name": "test_1",
     *                          "start_time": "2021-12-06 10:00:00",
     *                          "end_time": "2021-12-06 11:00:00",
     *                          "product_list": "AAA10mg"
     *                      }},
     *                      "stock_type": "20",
     *                      "action_id": "1",
     *                      "convention_status_item": {{
     *                          "status_item_name": "企画時出席予定"
     *                      }},
     *                      "medical_staff": {{
     *                          "medical_staff_cd": "C010",
     *                          "medical_staff_cd": "研修医"
     *                      }},
     *                      "input_deadline": 3,
     *                      "part_list": {{
     *                          "definition_value": "10",
     *                          "definition_label": "講師"
     *                      }},
     *                      "status_item_list": {{
     *                          "definition_value": "10",
     *                          "definition_label": "出席"
     *                      }},
     *                      "other_attendee": {{
     *                          "occupation_type": "10",
     *                          "occupation_name": "医師",
     *                          "other_headcount": {{
     *                                  "status_item_cd": "100",
     *                                  "headcount": 0
     *                          }}
     *                      }}
     *                 }}
     *             }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getScreenDataAttendant(SearchAttendantRequest $request)
    {
        try{
            $convention_id = $request->convention_id;
            $user_cd = $this->getCurrentUser();
            $params['org'] = $this->organization_service->getMainOrganizationsByUser($user_cd);
            $params['convention_id'] = $convention_id;
            $result = $this->service->getInfoConvention($params);
            return $this->responseSuccess('success', $result);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('convention.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/attendant-management/search",
     *      operationId="getData",
     *      tags={"C01-S03"},
     *      summary="Get data",
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
     *                      property="convention_id",
     *                      type="string",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="attendance",
     *                      type="string",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="unfollow",
     *                      type="string",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="limit",
     *                      type="string",
     *                      default="100"
     *                  ),
     *                  @OA\Property(
     *                      property="offset",
     *                      type="string",
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
     *                     }}
     *                 }
     *              )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getData(SearchAttendantRequest $request)
    {
        try{
            $request->unfollow = $request->unfollow ?? 0;
            $request->attendance = $request->attendance ?? 0;
            $request->limit = $request->filled('limit') ? $request->limit : 100;
            $request->offset = $request->filled('offset') ? $request->offset : 0;
            $user_cds = empty($request->user_cd) ? [] : $request->user_cd;
            if(count($user_cds) > 1){
                $result['records'] = [];
                $result['pagination'] = [];
            }else{
                $result = $this->service->searchData($request);
            }
            return $this->responseSuccess('success', $result);
        }catch(Exception $ex){
            throw($ex);
            return $this->responseSystemError(__('convention.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/attendant-management/add-convention-attendee",
     *      operationId="addConventionAttendee",
     *      tags={"C01-S03"},
     *      summary="Add Attendee",
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
     *                      property="convention_id",
     *                      type="string",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="convention_attendee",
     *                      type="string",
     *                      default="[]"
     *                  ),
     *                  @OA\Property(
     *                      property="other_attendee",
     *                      type="string",
     *                      default="[]"
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
    public function addConventionAttendee(AddAttendantRequest $request)
    {
        DB::beginTransaction();
        try{
            $user_cd = $this->getCurrentUser();
            $request->user_cd = $user_cd;
            $request->roles = $this->role->getRoleList($user_cd);
            $request->org = $this->organization_service->getMainOrganizationsByUser($user_cd);
            $result = $this->service->addData($request);
            if($result == 400){
                return $this->responseErrorValidate(__('convention.person_exists'));
            }
            DB::commit();
            return $this->responseCreated(__('convention.create_successfully'));
        }catch(Exception $ex){
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('convention.system_error'));
        }
    }
}
