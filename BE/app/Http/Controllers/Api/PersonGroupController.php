<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Illuminate\Http\Request;
use App\Services\PersonGroupService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PersonGroup\DeletePersonGroupRequest;
use App\Http\Requests\Api\PersonGroup\UpdatePersonGroupRequest;
use App\Http\Requests\Api\PersonGroup\ChangeSelectPersonGroupRequest;
use App\Http\Requests\Api\FacilityGroup\SelectFacilityGroupRequest;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PersonGroupController extends Controller
{
    private $service;

    public function __construct(PersonGroupService $service)
    {
        $this->service = $service;
        $this->middleware('role.not:'.config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/list-person-group",
     *      operationId="listPersonGroup",
     *      tags={"Z03-S02"},
     *      summary="Get list Person Group",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getData(SelectFacilityGroupRequest $request)
    {
        $request->user_login = $this->getCurrentUser();
        $data = $this->service->getData($request);
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/list-person-group/delete",
     *      operationId="deletePersonGroup",
     *      tags={"Z03-S02"},
     *      summary="Delete Person Group",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="select_person_group_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=301, description="Successful response",
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
    public function deletePersonGroup(DeletePersonGroupRequest $request)
    {
        DB::beginTransaction();
        try {
            $select_person_group_id = $request->select_person_group_id;
            $this->service->deletePersonGroup($select_person_group_id);
            DB::commit();
            return $this->responseNoContent(__('success'));
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\GET(
     *      path="/api/list-person-group/update",
     *      operationId="UpdatePersonGroup",
     *      tags={"Z03-S02"},
     *      summary="Update Person Group",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="select_person_group_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=201, description="Successful response",
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
    public function updatePersonGroup(UpdatePersonGroupRequest $request)
    {
        DB::beginTransaction();
        try {
            $request->user_cd = $this->getCurrentUser();
            $this->service->updatePersonGroup($request);
            DB::commit();
            return $this->responseCreated('success');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }

    /**
     * @OA\PUT(
     *      path="/api/list-person-group/change-select",
     *      operationId="ChangeSelectPersonGroup",
     *      tags={"Z03-S02"},
     *      summary="Change Select Person Group",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="person",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="person.*.sort_order",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="person.*.select_person_group_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=201, description="Successful response",
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
    public function changeSelectPersonGroup(ChangeSelectPersonGroupRequest $request)
    {
        DB::beginTransaction();
        try {
            $this->service->changeSelectPersonGroup($request);
            DB::commit();
            return $this->responseCreated('success');
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('auth.system_error'));
        }
    }
}
