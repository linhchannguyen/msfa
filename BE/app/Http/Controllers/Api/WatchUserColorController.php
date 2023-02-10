<?php

namespace App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\WatchUserColorService;
use App\Http\Requests\Api\UserColor\ChangeColorRequest;
use App\Http\Requests\Api\UserColor\GetWatchUserRequest;

class WatchUserColorController extends Controller
{
    private $service;

    public function __construct(WatchUserColorService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/watch-user-color/get-screen-data-calendar",
     *      operationId="getScreenDataCalendar",
     *      tags={"A01-S02"},
     *      summary="Get screen data.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {
     *                      "variable_definition": {
     *                      {
     *                          "definition_value": "10",
     *                          "definition_label": "面談"
     *                      }}
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getScreenDataCalendar()
    {
        try{
            $result = $this->service->getVariableDefinition();
            return $this->responseSuccess('success', $result);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('watchusercolor.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/watch-user-color/get-list-by-user-login",
     *      operationId="getWatchUserList",
     *      tags={"A01-S02"},
     *      summary="Get watch user list.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {
     *                      {
     *                          "watch_user_cd": "10001",
     *                          "display_flag": 0,
     *                          "user_cd": "10009",
     *                          "user_name": "小峯 嘉彦",
     *                          "display_color_cd": "40",
     *                          "display_color": "#FFE2BA",
     *                          "user_post_type": "20"
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getWatchUserList()
    {
        try{        
            $user_cd = $this->getCurrentUser();
            $watchUserList = $this->service->getWatchUserList($user_cd);
            return $this->responseSuccess('success', $watchUserList);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('watchusercolor.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/watch-user-color/get-list-by-user-and-type",
     *      operationId="ListActivitySchedule",
     *      tags={"A01-S02"},
     *      summary="Get list the activity schedule of colleagues and subordinates.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="startDate",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="endDate",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="user_cd",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="type",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {
     *                      {
     *                          "schedule_id": 1,
     *                          "schedule_type": "10",
     *                          "start_date": "2019/04/01",
     *                          "start_time": "2021/04/01 10:00:00",
     *                          "end_time": "2021/04/01 10:30:00",
     *                          "title": " 説明会",
     *                          "all_day_flag": 0,
     *                          "display_option_type": "面談",
     *                          "user_cd": "10001",
     *                          "display_color_cd": null,
     *                          "display_color": null
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getListScheduleByUserAndType(GetWatchUserRequest $request)
    {
        try{
            $request_user_cds = empty($request->user_cd) ? [] : $request->user_cd;
            $request_types = empty($request->type) ? [] : $request->type;
            $user_cds = [];
            $types = [];
            foreach($request_user_cds as $user_cd){
                if(!empty($user_cd)){
                    array_push($user_cds, $user_cd);
                }
            }
            foreach($request_types as $type){
                if(!empty($type)){
                    array_push($types, $type);
                }
            }
            $data = [];
            if (!empty($types) && !empty($user_cds)) {
                $data = $this->service->getListByUser($request->startDate,$request->endDate, $user_cds, $types, $this->getCurrentUser());
            }
            return $this->responseSuccess('success', $data);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('watchusercolor.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/watch-user-color/change-color",
     *      operationId="ChangeColorUser",
     *      tags={"A01-S02"},
     *      summary="Watch User Color",
     *      description="Change color user",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Change color params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="user_cd",
     *                      type="string",
     *                      default="[10003]"
     *                  ),
     *                  @OA\Property(
     *                      property="display_color_cd",
     *                      type="string",
     *                      default="10"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="success"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     *    )
     */

    public function changeColorUser(ChangeColorRequest $request)
    {
        DB::beginTransaction();
        try{
            $user_cds = !is_array($request->user_cd) ? [] : $request->user_cd;
            $display_color_cds = !is_array($request->display_color_cd) ? [] : $request->display_color_cd;
            $this->service->changeColorUser($user_cds, $display_color_cds, $request->display_flag, $this->getCurrentUser());
            DB::commit();
            return $this->responseCreated(__('watchusercolor.save_successfully'));
        }catch(Exception $ex){
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('watchusercolor.system_error'));
        }
    }

    /**
     * @OA\DELETE(
     *      path="/api/watch-user-color/delete/{watch_user_cd}",
     *      operationId="deleteWatchUser",
     *      tags={"A01-S02"},
     *      summary="Delete watch user.",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="success"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function deleteWatchUser($user_cd)
    {
        DB::beginTransaction();
        try{
            $currentUser = $this->getCurrentUser();
            $this->service->deleteWatchUser($currentUser, $user_cd);
            DB::commit();
            return $this->responseSuccess(__('watchusercolor.delete_successfully'));
        }catch(Exception $ex){
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('watchusercolor.system_error'));
        }
    }
}