<?php

namespace App\Http\Controllers\Api;

use App\Services\MessageService;
use Illuminate\Support\Facades\DB;
use App\Services\RolePolicyService;
use App\Http\Controllers\Controller;
use App\Transformers\MessageTransformer;
use App\Http\Requests\Api\Message\MessageRequest;
use App\Http\Requests\Api\Message\MessageInfoRequest;

class MessageController extends Controller
{
    private $service;
    private $role;

    public function __construct(MessageService $service, RolePolicyService $role)
    {
        $this->service = $service;
        $this->role = $role;
        $this->middleware('role.has:'.config('role.MESSAGE_MG.CODE').','.config('role.SYS_MG.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/message",
     *      operationId="allMessage",
     *      tags={"Z02-S02"},
     *      summary="Get list message",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                          {
     *                            "message_id": 3,
     *                            "message_subject": "subjesubjesubjesubjesubjesubj2",
     *                            "release_start_date": "2021-10-22",
     *                            "release_end_date": "2021-10-22",
     *                            "release_org_cd": "o3",
     *                            "org_name": "QA",
     *                            "sender_name": "sender_namesender_namesender_names",
     *                            "message_contents": "contents",
     *                            "important_flag": 0,
     *                            "last_update_datetime": "2021-10-29 12:17:10",
     *                            "create_user_cd": "4"
     *                          }
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function allMessage()
    {
        $data = $this->service->getMessageList();
        $result = empty($data) ? [] : MessageTransformer::collection($data);

        return $this->responseSuccess('success', $result);
    }

    /**
     * @OA\GET(
     *      path="/api/message/{id}",
     *      operationId="getMessageInfo",
     *      tags={"Z02-S02"},
     *      summary="Get info message",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="id",in="query", @OA\Schema(type="integer")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status":"success",
     *                     "data":{
     *                         "message_id": 1024,
     *                         "message_subject": "Test tesst",
     *                         "release_start_date": "2021-11-04",
     *                         "release_end_date": "2021-11-07",
     *                         "release_org_cd": "11133",
     *                         "org_name": "福島エリア",
     *                         "sender_name": "MSFA tester",
     *                         "message_contents": "Test",
     *                         "important_flag": 1,
     *                         "last_update_datetime": "2021-11-05 11:17:38",
     *                         "create_user_cd": "tutx"
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getMessageInfo($id, MessageInfoRequest $request)
    {
        $data[] = $this->service->getInfo($id);
        $result = empty($data[0]) ? [] : MessageTransformer::collection($data);

        return $this->responseSuccess('success', $result);
    }

    /**
     * @OA\POST(
     *      path="/api/message",
     *      operationId="createMessage",
     *      tags={"Z02-S02"},
     *      summary="Create message",
     *      description="Create message",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Read message",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="important_flag",
     *                      type="integer",
     *                      description="important flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="message_subject",
     *                      type="string",
     *                      description="message subject",
     *                      default="subject"
     *                  ),
     *                  @OA\Property(
     *                      property="release_start_date",
     *                      type="string",
     *                      description="release start date",
     *                      default="2021/10/26"
     *                  ),
     *                  @OA\Property(
     *                      property="release_end_date",
     *                      type="string",
     *                      description="release end date",
     *                      default="2021/10/26"
     *                  ),
     *                  @OA\Property(
     *                      property="release_org_cd",
     *                      type="string",
     *                      description="release org cd",
     *                      default="10000"
     *                  ),
     *                  @OA\Property(
     *                      property="sender_name",
     *                      type="string",
     *                      description="sender name",
     *                      default="sender name"
     *                  ),
     *                  @OA\Property(
     *                      property="message_contents",
     *                      type="string",
     *                      description="message contents",
     *                      default="message contents"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=400, description="Input invalid Responser"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function createMessage(MessageRequest $request)
    {
        $request->user_cd = $this->getCurrentUser();
        $request->release_start_date = $this->convDATE($request->release_start_date);
        $request->release_end_date = $this->convDATE($request->release_end_date);

        DB::beginTransaction();
        $result = $this->service->create($request);
        if(!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('messages.system_error'));
        }

        DB::commit();
        return $this->responseCreated(__('messages.save_successfully'));
    }

    /**
     * @OA\PUT(
     *      path="/api/message/{id}",
     *      operationId="updateMessage",
     *      tags={"Z02-S02"},
     *      summary="Update message",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Read message",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="important_flag",
     *                      type="integer",
     *                      description="important flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="message_subject",
     *                      type="string",
     *                      description="message subject",
     *                      default="subject"
     *                  ),
     *                  @OA\Property(
     *                      property="release_start_date",
     *                      type="string",
     *                      description="release start date",
     *                      default="2021/10/26 16:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="release_end_date",
     *                      type="string",
     *                      description="release end date",
     *                      default="2021/10/26 16:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="release_org_cd",
     *                      type="string",
     *                      description="release org cd",
     *                      default="10000"
     *                  ),
     *                  @OA\Property(
     *                      property="sender_name",
     *                      type="string",
     *                      description="sender name",
     *                      default="sender name"
     *                  ),
     *                  @OA\Property(
     *                      property="message_contents",
     *                      type="string",
     *                      description="message contents",
     *                      default="message contents"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201, description="Successful response"),
     *      @OA\Response(response=400, description="Input invalid Responser"),
     *      @OA\Response(response=403, description="Forbidden Response"),
     *      @OA\Response(response=500, description="System Error or Unknow Error Response")
     * )
     */
    public function updateMessage(MessageRequest $request, $id)
    {
        DB::beginTransaction();
        $request->message_id = $id;
        $request->user_cd = $this->getCurrentUser();
        $this->checkAction($id, $request->user_cd);
        $request->release_start_date = $this->convDATE($request->release_start_date);
        $request->release_end_date = $this->convDATE($request->release_end_date);
        $result = $this->service->update($request, $id);
        if(!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('messages.system_error'));
        }
        DB::commit();
        return $this->responseCreated(__('messages.update_successfully'));
    }

    /**
     * @OA\DELETE(
     *      path="/api/message/{id}",
     *      operationId="deleteMessage",
     *      tags={"Z02-S02"},
     *      summary="Delete message",
     *      description="Delete message by message_id",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response"),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function deleteMessage($id)
    {
        $user_cd = $this->getCurrentUser();
        $this->checkAction($id, $user_cd);
        DB::beginTransaction();
        $result = $this->service->delete($id);
        if(!$result) {
            DB::rollBack();
            return $this->responseSystemError(__('messages.system_error'));
        }
        DB::commit();
        return $this->responseSuccess(__('messages.delete_successfully'));
    }

    protected function convDATE($dateStr, $format = 'Y-m-d H:i:s')
    {
        if (($dateStr == '0000/00/00') || empty($dateStr) || ($dateStr == '0000-00-00')) {
            $date = '0000-00-00';
        } else {
            $date = date($format, strtotime($dateStr));
        }
        return $date;
    }

    protected function checkAction($id, $user_cd)
    {
        $user_info = $this->getInfoCurrentUser($user_cd, IS_USER);
        $roles = $this->role->getRoleList($user_cd);
        $message_creator = '';
        $system_admin = '';
        if (in_array('R70', $roles)){
            $message_creator = 'R70';
        }
        if (in_array('R90', $roles)){
            $system_admin = 'R90';
        }
        $message_info = $this->service->getInfo($id);
        $create_user_cd = $message_info->create_user_cd ?? "";
        if(($message_creator == "R70" && $user_cd == $create_user_cd) || $system_admin == "R90"){
            return true;
        }
        abort($this->responseErrorForbidden(__('messages.access_denied', ['attribute' => $user_info->user_name ?? ""])));
    }
}
