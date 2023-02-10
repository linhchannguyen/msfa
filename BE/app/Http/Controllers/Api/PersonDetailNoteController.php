<?php

namespace App\Http\Controllers\Api;

use stdClass;
use App\Traits\DateTimeTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\OrganizationService;
use App\Services\PersonDetailNoteService;
use App\Transformers\PersonDetailNotesTransformer;
use App\Http\Requests\Api\PersonDetailNote\PersonDetailNoteRequest;
use App\Http\Requests\Api\PersonDetailNote\CreatePersonDetailNoteRequest;
use App\Http\Requests\Api\PersonDetailNote\DeletePersonDetailNoteRequest;
use App\Http\Requests\Api\PersonDetailNote\UpdatePersonDetailNoteRequest;
use Exception;

class PersonDetailNoteController extends Controller
{
    use DateTimeTrait;
    private $service, $organization_service;

    public function __construct(PersonDetailNoteService $service, OrganizationService $organization_service)
    {
        $this->service = $service;
        $this->organization_service = $organization_service;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/person-detail-notes/get-screen-data",
     *      operationId="getScreenDataNotes",
     *      tags={"H02-S05"},
     *      summary="Get Screen Data",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *          @OA\JsonContent(
     *              type="object",
     *              example={
     *                  "status":"success",
     *                  "data":{
     *                      "considerationType": {
     *                          {
     *                              "consideration_type": "10",
     *                              "consideration_type_name": "訪問規制",
     *                              "use_facility_flag": 1,
     *                              "use_person_flag": 1
     *                          },
     *                          {
     *                              "consideration_type": "20",
     *                              "consideration_type_name": "薬審",
     *                              "use_facility_flag": 1,
     *                              "use_person_flag": 1
     *                          }
     *                      }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function getScreenDataNotes()
    {
        try {
            $data['considerationType'] = $this->service->allConsiderationType();
            return $this->responseSuccess("success", $data);
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/person-detail-notes/search/{id}",
     *      operationId="searchPersonDetailNotes",
     *      tags={"H02-S05"},
     *      summary="Search Person Detail Notes",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Search Person Detail Notes",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="last_update_datetime_from",
     *                      type="string",
     *                      description="last update datetime from",
     *                      default="2021/01/01"
     *                  ),
     *                  @OA\Property(
     *                      property="last_update_datetime_to",
     *                      type="string",
     *                      description="last update datetime to",
     *                      default="2022-01-01"
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_type",
     *                      type="string",
     *                      description="consideration type",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_contents",
     *                      type="integer",
     *                      description="consideration contents",
     *                      default=""
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                         "notes": {
     *                             {
     *                                 "person_consideration_id": 2,
     *                                 "person_cd": "01101001",
     *                                 "consideration_type": "20",
     *                                 "consideration_type_name": "薬審",
     *                                 "consideration_contents": "Test",
     *                                 "last_update_datetime": "2021/12/13",
     *                                 "create_user_cd": "tutx",
     *                                 "create_user_name": "Tu",
     *                                 "create_org_cd": "11000",
     *                                 "create_org_label": "test",
     *                                 "updated_by": "10009",
     *                                 "user_confirm_list": {
     *                                     {
     *                                         "user_cd": "10001",
     *                                         "user_name": "安永 みづほ",
     *                                         "confirmed_flag": 0,
     *                                         "confirmed_datetime": "2021-12-09 13:04:36"
     *                                     },
     *                                     {
     *                                         "user_cd": "tutx",
     *                                         "user_name": "安永 みづほ",
     *                                         "confirmed_flag": 0,
     *                                         "confirmed_datetime": "2021-12-09 13:04:36"
     *                                     }
     *                                 }
     *                             },
     *                         },
     *                         "person_in_charge_list": {
     *                             {
     *                                 "user_cd": "10036"
     *                             }
     *                         }
     *                     }
     *                 }
     *              )
     *      ),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="External System Error")
     * )
     */
    public function searchPersonDetailNotes($person_cd, PersonDetailNoteRequest $request)
    {
        try{
            $request->person_cd = $person_cd;
            $consideration_types = ($request->consideration_type != "") ? explode(",", trim($request->consideration_type)) : [];
            $request->consideration_types = $consideration_types;
            $person_in_charge_list = $this->service->getPersonInChargeList($person_cd);
            $datas = $this->service->searchConsideration($request);
            if($consideration_types){
                $result['notes'] = empty($datas) ? [] : PersonDetailNotesTransformer::collection($datas);
            }else{
                $result['notes'] = [];
            }
            $result['person_in_charge_list'] = $person_in_charge_list;
            return $this->responseSuccess('success', $result);
        }catch(Exception $ex){
            throw $ex;
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
    }

    /**
     * @OA\PUT(
     *      path="/api/person-detail-notes/person-consideration-confirm/{id}",
     *      operationId="personConsiderationConfirm",
     *      tags={"H02-S05"},
     *      summary="Person consideration confirm",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function personConsiderationConfirm($person_consideration_id)
    {
        DB::beginTransaction();
        try{
            $user_cd =  $this->getCurrentUser();
            $request = new stdClass;
            $request->person_consideration_id = $person_consideration_id;
            $request->user_cd = $user_cd;
            $request->confirmed_flag = 1;
            $this->service->considerationConfirm($request);
            DB::commit();
            return $this->responseCreated(__('persondetailnotes.update_successfully'));
        }catch(Exception $ex){
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
    }

    /**
     * @OA\POST(
     *      path="/api/person-detail-notes/create",
     *      operationId="createPersonDetailNotes",
     *      tags={"H02-S05"},
     *      summary="Create Person Detail Notes",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Create Person Detail Notes",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      description="person cd",
     *                      default="01101001"
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_type",
     *                      type="string",
     *                      description="consideration type",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_contents",
     *                      type="string",
     *                      description="consideration contents",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="person_consideration_confirm",
     *                      type="integer",
     *                      description="person consideration confirm",
     *                      default="tutx,10001"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function createPersonDetailNotes(CreatePersonDetailNoteRequest $request)
    {
        DB::beginTransaction();
        try{
            $datas = [];
            $user_login = IS_USER;
            $user_cd =  $this->getCurrentUser();
            $user =  $this->getInfoCurrentUser($user_cd, $user_login);
            $org = $this->organization_service->getMainOrganizationsByUser($user_cd);
            //Data t_person_consideration
            $datas['t_person_consideration'][0]['person_cd'] = $request->person_cd;
            $datas['t_person_consideration'][0]['consideration_type'] = $request->consideration_type;
            $datas['t_person_consideration'][0]['consideration_contents'] = $request->consideration_contents ?? "";
            $datas['t_person_consideration'][0]['last_update_datetime'] = $this->currentJapanDateTime('Y-m-d H:i:s');
            $datas['t_person_consideration'][0]['create_user_cd'] = $user_cd;
            $datas['t_person_consideration'][0]['create_user_name'] = $user->user_name ?? "";
            $datas['t_person_consideration'][0]['create_org_cd'] = $org->org_cd ?? "";
            $datas['t_person_consideration'][0]['create_org_label'] = $org->org_label ?? "";
            //Data t_person_consideration_confirm
            //Insert Personal Consideration Notes
            $this->service->createPersonConsideration($datas['t_person_consideration']);
            $person_consideration_confirm = explode(',', $request->person_consideration_confirm);
            $person_consideration_confirms = [];
            foreach($person_consideration_confirm as $value){
                if(!empty($value)){
                    array_push($person_consideration_confirms, $value);
                }
            }
            if(!empty($person_consideration_confirms)){
                $person_confirms = [];
                $last_person_consideration = $this->service->lastInsertedPersonConsideration();
                foreach ($person_consideration_confirms as $value) {
                    array_push($person_confirms, [
                        'person_consideration_id' => $last_person_consideration->person_consideration_id,
                        'user_cd' => $value
                    ]);
                    $inform_contents = __('persondetailnotes.inform_contents', ['username' => $user->user_name, 'userassign' => $request->person_name ?? NULL]);
                    $inform['user_cd'] = $user_cd;
                    $inform['user_cd_assign'] = $value;
                    $inform['inform_contents'] = $inform_contents;
                    $inform['person_cd'] = $request->person_cd;
                    //Insert Inform
                    $this->service->createInform($inform);
                }
                $datas['t_person_consideration_confirm'] = $person_confirms;
                //Insert Personal Consideration Notes Confirm
                $this->service->createPersonConsiderationConfirm($datas['t_person_consideration_confirm']);
            }
            DB::commit();
            return $this->responseCreated(__('persondetailnotes.create_successfully'));
        }catch(Exception $ex){
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
    }

    /**
     * @OA\PUT(
     *      path="/api/person-detail-notes/update",
     *      operationId="updatePersonDetailNotes",
     *      tags={"H02-S05"},
     *      summary="Update Person Detail Notes",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Update Person Detail Notes",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="person_consideration_id",
     *                      type="string",
     *                      description="person consideration id",
     *                      default="21"
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      description="person cd",
     *                      default="01101001"
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_type",
     *                      type="string",
     *                      description="consideration type",
     *                      default="10"
     *                  ),
     *                  @OA\Property(
     *                      property="consideration_contents",
     *                      type="string",
     *                      description="consideration contents",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="person_consideration_confirm",
     *                      type="integer",
     *                      description="person consideration confirm",
     *                      default="tutx,10001"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function updatePersonDetailNotes(UpdatePersonDetailNoteRequest $request)
    {
        DB::beginTransaction();
        try{
            //Check action
            $this->checkAction($request);
    
            $datas = [];
            $user_login = IS_USER;
            $user_cd =  $this->getCurrentUser();
            $user =  $this->getInfoCurrentUser($user_cd, $user_login);
            //Data t_person_consideration
            $datas['t_person_consideration']['person_consideration_id'] = $request->person_consideration_id;
            $datas['t_person_consideration']['consideration_type'] = $request->consideration_type;
            $datas['t_person_consideration']['consideration_contents'] = $request->consideration_contents ?? "";
            $datas['t_person_consideration']['last_update_datetime'] = $this->currentJapanDateTime('Y-m-d H:i:s');
            $person_consideration_confirm = explode(',', $request->person_consideration_confirm);
            $person_consideration_confirms = [];
            foreach($person_consideration_confirm as $value){
                if(!empty($value)){
                    array_push($person_consideration_confirms, $value);
                }
            }
            $user_cd_consideration_confirm_old = $this->service->getConsiderationConfirmById($request->person_consideration_id);
            $add_new_list = $this->findAddNewUserConfirm($user_cd_consideration_confirm_old, $person_consideration_confirms);
            $datas['user_cd_remove']['user_cd'] = $this->findRemoveUserConfirm($user_cd_consideration_confirm_old, $person_consideration_confirms);
            $datas['user_cd_remove']['person_consideration_id'] = $request->person_consideration_id;
            //Data t_person_consideration_confirm
            //Update Personal Consideration Notes
            $this->service->updatePersonConsideration($datas['t_person_consideration']);
            $person_confirms = [];
            foreach ($add_new_list as $value) {
                array_push($person_confirms, [
                    'person_consideration_id' => $request->person_consideration_id,
                    'user_cd' => $value
                ]);
                $inform_contents = __('persondetailnotes.inform_contents', ['username' => $user->user_name, 'userassign' => $request->person_name ?? NULL]);
                $inform['user_cd'] = $user_cd;
                $inform['user_cd_assign'] = $value;
                $inform['inform_contents'] = $inform_contents;
                $inform['person_cd'] = $request->person_cd;
                //Update/Insert Inform
                $this->service->createInform($inform);
            }
            if (!empty($person_confirms)) {
                $datas['t_person_consideration_confirm'] = $person_confirms;
                //Update Personal Consideration Notes Confirm
                $this->service->createPersonConsiderationConfirm($datas['t_person_consideration_confirm']);
            }else{
                $data = new stdClass;
                $data->confirmed_flag = 0;
                foreach($user_cd_consideration_confirm_old as $value){
                    $data->user_cd = $value->user_cd;
                    $data->person_consideration_id = $value->person_consideration_id;
                    $this->service->considerationConfirm($data);
                }
            }
            $this->service->deleteConsiderationConfirm($datas['user_cd_remove']);
            DB::commit();
            return $this->responseCreated(__('persondetailnotes.create_successfully'));
        }catch(Exception $ex){
            DB::rollBack();
            throw $ex;
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
    }

    protected function findAddNewUserConfirm($confirm_list_old, $confirm_list_new)
    {
        $result = [];
        foreach ($confirm_list_new as $user_cd_new) {
            $exists = 0;
            foreach ($confirm_list_old as $user_cd_old) {
                if ($user_cd_new == $user_cd_old->user_cd) {
                    $exists++;
                }
            }
            if ($exists == 0) {
                $result[] = $user_cd_new;
            }
        }
        return $result;
    }

    protected function findRemoveUserConfirm($confirm_list_old, $confirm_list_new)
    {
        $result = [];
        foreach ($confirm_list_old as $user_cd_old) {
            $exists = 0;
            foreach ($confirm_list_new as $user_cd_new) {
                if ($user_cd_new == $user_cd_old->user_cd) {
                    $exists++;
                }
            }
            if ($exists == 0) {
                $result[] = $user_cd_old->user_cd;
            }
        }
        return $result;
    }

    /**
     * @OA\DELETE(
     *      path="/api/person-detail-notes/delete",
     *      operationId="deletePersonDetailNotes",
     *      tags={"H02-S05"},
     *      summary="Delete Person Detail Notes",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Create Person Detail Notes",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="person_consideration_id",
     *                      type="string",
     *                      description="person consideration id",
     *                      default="16"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=200, description="Successful response"),
     *      @OA\Response(response=400, description="Validation Error"),
     *      @OA\Response(response=403, description="Forbidden"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function deletePersonDetailNotes(DeletePersonDetailNoteRequest $request)
    {
        DB::beginTransaction();
        //Check action
        $this->checkAction($request);
        //Delete data
        $result = $this->service->deleteConsideration($request->person_consideration_id);
        if(!$result){
            DB::rollBack();
            return $this->responseSystemError(__('persondetailnotes.system_error'));
        }
        DB::commit();
        return $this->responseSuccess(__('persondetailnotes.delete_successfully'));
    }

    protected function checkAction($request)
    {
        $user_cd = $this->getCurrentUser();
        $user_info = $this->getInfoCurrentUser($user_cd, IS_USER);
        $person_in_charge_list = $this->service->getPersonInChargeList($request->person_cd);
        $consideration = $this->service->getConsiderationById($request->person_consideration_id);
        //ログインユーザが個人の所属する施設の担当者の場合
        foreach ($person_in_charge_list as $person) {
            if ($user_cd == $person->user_cd) {
                return true;
            }
        }
        $updated_by = $consideration[0]->updated_by;
        $create_user_cd = $consideration[0]->create_user_cd ?? "";
        //ログインユーザが個人の所属する施設の担当者の場合
        if ($updated_by == $user_cd && $create_user_cd != $user_cd) {
            return true;
        }
        //Notes creator will be edit/delete
        if ($create_user_cd == $user_cd) {
            return true;
        }
        abort($this->responseErrorForbidden(__('persondetailnotes.access_denied', ['attribute' => $user_info->user_name ?? ""])));
    }
}
