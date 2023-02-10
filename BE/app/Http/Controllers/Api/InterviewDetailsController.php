<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InterviewDetails\AddInterviewDestinationRequest;
use App\Http\Requests\Api\InterviewDetails\AddStockRequest;
use App\Http\Requests\Api\InterviewDetails\CheckPersonCdRequest;
use App\Http\Requests\Api\InterviewDetails\CreatePlanScheduleRequest;
use App\Http\Requests\Api\InterviewDetails\DeleteAllInterviewRequest;
use App\Http\Requests\Api\InterviewDetails\DeleteInternalScheduleRequest;
use App\Http\Requests\Api\InterviewDetails\DeleteInterviewerRequest;
use App\Http\Requests\Api\InterviewDetails\DeletePrivateScheduleRequest;
use App\Http\Requests\Api\InterviewDetails\DeleteScheduleRequest;
use App\Http\Requests\Api\InterviewDetails\GetActiveDateRequest;
use App\Http\Requests\Api\InterviewDetails\GetDateTimeSettingRequest;
use App\Http\Requests\Api\InterviewDetails\GetInternalScheduleRequest;
use App\Http\Requests\Api\InterviewDetails\GetInterviewContentRequest;
use App\Http\Requests\Api\InterviewDetails\GetPrivateScheduleRequest;
use App\Http\Requests\Api\InterviewDetails\SaveDateTimeSettingRequest;
use App\Http\Requests\Api\InterviewDetails\SaveInternalScheduleRequest;
use App\Http\Requests\Api\InterviewDetails\SavePrivateScheduleRequest;
use App\Http\Requests\Api\UserManagement\CreateUserRequest;
use App\Services\InterviewDetailsService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Traits\DateTimeTrait;
use App\Services\AuthService;
use App\Services\SystemParameterService;

class InterviewDetailsController extends Controller
{
    use DateTimeTrait;
    private $interviewDetails, $auth;

    public function __construct(InterviewDetailsService $interviewDetails, AuthService $auth, SystemParameterService $systemParameterService)
    {
        $this->interviewDetails = $interviewDetails;
        $this->auth = $auth;
        $this->systemParameterService = $systemParameterService;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     *  @OA\GET(
     *      path="/api/interview-details/get-screen-data",
     *      operationId="getScreenDataInterviewDetails",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Get Screen Data",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                           "title_office": {
     *                               {
     *                                   "office_schedule_type": "10",
     *                                   "office_schedule_type_name": "社内会議",
     *                                   "title_free_flag" : 0
     *                               },
     *                               {
     *                                   "office_schedule_type": "20",
     *                                   "office_schedule_type_name": "研修",
     *                                   "title_free_flag" : 0
     *                               }
     *                           }
     *                       }
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getScreenData()
    {
        $data = $this->interviewDetails->getScreenData();
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\GET(
     *      path="/api/interview-details/get-active-date",
     *      operationId="getActiveDate",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Get Active Date",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                           "status": "200",
     *                           "message": "success",
     *                           "data": {
     *                               "important_flag": 1,
     *                               "start_date": "2021-12-06",
     *                               "start_time": "2021-12-06 11:00:00",
     *                               "end_time": "2021-12-06 13:00:00",
     *                               "facility_short_name": "田口医師会保健医療センター（札幌市中央区）",
     *                               "schedule_type": "20",
     *                               "schedule_id": 2,
     *                               "display_option_type": "面談",
     *                               "definition_label": "面談",
     *                               "facility_cd": "011010003",
     *                               "report_status_type": "未作成"
     *                           }
     *                       }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getActiveDate(GetActiveDateRequest $request)
    {
        $data = $this->interviewDetails->getActiveDate($request->schedule_id);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\GET(
     *      path="/api/interview-details/get-interview-content",
     *      operationId="GetInterviewContent",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Get Interview Content",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *      type="object",
     *      example={
     *                       "status": "200",
     *                       "message": "success",
     *                       "data": {
     *                                  {
     *                                      "department_name": "その他",
     *                                      "person_name": "和田 晴生",
     *                                      "person_cd": "01101002",
     *                                      "position_name": "役職なし",
     *                                      "plan_flag": 0,
     *                                      "act_flag": 0,
     *                                      "call_id": 2,
     *                                      "facility_cd": "011010002",
     *                                      "visit_id": 2,
     *                                      "user_cd": "10002",
     *                                      "dataTable": {
     *                                          {
     *                                              "detail_id": 1023,
     *                                              "detail_order": 2,
     *                                              "content_name": "WEB会議",
     *                                              "product_name": "AAA製品100mg",
     *                                              "data": {
     *                                                  {
     *                                                      "document_id": "2",
     *                                                      "start_date": "2021-10-01",
     *                                                      "end_date": "2021-12-01",
     *                                                      "document_name": "test"
     *                                                  }
     *                                              }
     *                                          }
     *                                      }
     *                                  }
     *                           }
     *                     }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getInterviewContent(GetInterviewContentRequest $request)
    {
        $data = $this->interviewDetails->getInterviewContent($request->schedule_id);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\GET(
     *      path="/api/interview-details/get-date-time-setting",
     *      operationId="GetDateTimeSetting",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Get Date Time Setting",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Response successfully",
     *      @OA\JsonContent(
     *      type="object",
     *      example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       "start_date": "2021-12-06",
     *                       "start_time": "2021-12-06 11:50:00",
     *                       "end_time": "2021-12-06 13:40:00",
     *                       "all_day_flag": 0,
     *                       "important_flag": 0,
     *                       "visit_id": 2,
     *                       "user_cd": "10001",
     *                       "remarks": null,
     *                       "accompanying_user": {
     *                           {
     *                               "org_label": "北海道東北支店",
     *                               "org_cd": "11100",
     *                               "org_short_name": "北海道東北支店",
     *                               "user_cd": "10003",
     *                               "accompanying_id": 1,
     *                               "user_name": "吉良 光浩",
     *                               "delete_flag": "0",
     *                               "change_flag": "0",
     *                               "status_type": "0",
     *                               "report_status_type": "0"
     *                           }
     *                       }
     *                   }
     *               }
     *            )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getDateTimeSetting(GetDateTimeSettingRequest $request)
    {
        $data = $this->interviewDetails->getDateTimeSetting($request->schedule_id);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\POST(
     *      path="/api/interview-details/save-date-time-setting",
     *      operationId="saveDateTimeSetting",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Save Date Time Setting",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Save Date Time Setting Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="Schedule Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="date",
     *                      description="Start Date",
     *                      default="2021-12-06"
     *                  ),
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="date",
     *                      description="Start Time",
     *                      default="2021-12-06 11:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="date",
     *                      description="End Time",
     *                      default="2021-12-06 13:00:00"
     *                  ),
     *
     *                  @OA\Property(
     *                      property="all_day_flag",
     *                      type="number",
     *                      description="All Day Flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="important_flag",
     *                      type="number",
     *                      description="Important Flag",
     *                      default="0"
     *                  ),
     *                  @OA\Property(
     *                      property="accompanying_user",
     *                      type="string",
     *                      description="Accompanying User",
     *                      default="[]"
     *                  ),
     *
     *                  @OA\Property(
     *                      property="visit_id",
     *                      type="number",
     *                      description="Visit Id",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="number",
     *                      description="Facility Short Name",
     *                      default="ABC"
     *                  ),
     *                  @OA\Property(
     *                      property="display_option_type",
     *                      type="string",
     *                      description="Display Option Type",
     *                      default="abc"
     *                  ),
     *                  @OA\Property(
     *                      property="remarks",
     *                      type="string",
     *                      description="Remarks",
     *                      default="abc"
     *                  ),
     *                  @OA\Property(
     *                      property="accompanying_id",
     *                      type="string",
     *                      description="Travel Id",
     *                      default="1"
     *                  ),
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
    public function saveDateTimeSetting(SaveDateTimeSettingRequest $request)
    {
        DB::beginTransaction();
        try {
            //param
            $start_date = $request->start_date;
            $start_date_old = $request->start_date_old ?? $start_date;
            $start_time = $request->start_time;
            $end_time = $request->end_time;
            $all_day_flag = $request->all_day_flag;
            $important_flag = $request->important_flag;
            $accompanying_user = $request->accompanying_user;
            $visit_id = $request->visit_id;
            $schedule_id = $request->schedule_id;
            $facility_short_name = $request->facility_short_name;
            $remarks = $request->remarks;

            if (date_create($start_time)->format('Y/m/d H:i:s') >= date_create($end_time)->format('Y/m/d H:i:s')) {
                return $this->responseErrorValidate(__('interviewdetails.end_date_validate', ['attribute' => '終了日時', 'value' => '開始日時']));
            }

            $accompanying_user_temp = array_column($accompanying_user, 'user_cd');
            $array_values = array_count_values($accompanying_user_temp);
            if (count($array_values) > 0) {
                foreach ($array_values as $key => $item) {
                    if ($item > 1) {
                        $key_temp = array_search($key, $accompanying_user_temp);
                        return $this->responseErrorValidate(__('interviewdetails.duplicate_accompanying_user', ['0' => '社内同行者', '1' => $accompanying_user[$key_temp]['user_name'] ?? '']));
                    }
                }
            }

            //get info user cd
            $user_cd =  $this->getCurrentUser();
            $data_user = $this->auth->getInfoUser($user_cd);

            // save visit
            $result = $this->interviewDetails->saveVisit($visit_id, $schedule_id, $data_user->user_name ?? '', $data_user->org_cd ?? '', $data_user->org_short_name ?? '', $important_flag, $remarks , $user_cd , $start_date,$start_date_old);
            if(!$result){
                return $this->responseErrorValidate(__('interviewdetails.select_date_report'));
            }
            // save schedule
            $this->interviewDetails->saveSchedule($schedule_id, $facility_short_name, $visit_id, $start_date, $start_time, $end_time, $all_day_flag);

            // save accompanying user
            $getAccompanyingUser = $this->interviewDetails->getAccompanyingUser($visit_id);
            $accompanying_user_old = array_column($getAccompanyingUser, 'user_cd');
            $this->interviewDetails->deleteAccompanyingUser($visit_id);
            $month = date_create($start_date)->format('m');
            $day = date_create($start_date)->format('d');

            $inform_contents = __('interviewdetails.inform_content', ['user_name' => $data_user->user_name, 'start_date' => $month . '月' . $day . '日', 'facility_name' => $facility_short_name]);
            if (count((array)$accompanying_user) > 0) {
                foreach ($accompanying_user as $item) {
                    $item = (object)$item;
                    if (!in_array($item->user_cd, $accompanying_user_old)) {
                        // create inform
                        $this->interviewDetails->createInform($schedule_id, $inform_contents, $item->user_cd, ACCOMPANYING_USER_INFORM_CATEGORY);
                    }
                    $this->interviewDetails->saveAccompanyingUser($visit_id, $item->user_cd, $item->user_name ?? '', $item->org_cd, $item->org_short_name ?? '');
                }
            }
            DB::commit();
            return $this->responseCreated(__('interviewdetails.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     *  @OA\DELETE(
     *      path="/api/interview-details/delete-schedule",
     *      operationId="deleteSchedule",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Delete Schedule",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete Schedule params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="number",
     *                      description="schedule id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="visit_id",
     *                      type="number",
     *                      description="Visit Id",
     *                      default="2"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="Delete successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "204",
     *                       "message": "正常に削除しました。",
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
    public function deleteSchedule(DeleteScheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            //param
            $schedule_id = $request->schedule_id;
            $visit_id = $request->visit_id;
            $result = $this->interviewDetails->deleteSchedule($schedule_id, $visit_id);
            if(!$result){
                return $this->responseErrorValidate(__('interviewdetails.delete_report', ['attribute' => '面談' ]));
            }
            DB::commit();
            return $this->responseCreated(__('interviewdetails.delete'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     *  @OA\DELETE(
     *      path="/api/interview-details/delete-all-interviewer",
     *      operationId="deleteAllInterview",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Delete All Interview",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete All Interview params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="number",
     *                      description="schedule id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="visit_id",
     *                      type="number",
     *                      description="Visit Id",
     *                      default="2"
     *                  ),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="Delete successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "204",
     *                       "message": "正常に削除しました。",
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
    public function deleteAllInterview(DeleteAllInterviewRequest $request)
    {
        DB::beginTransaction();
        try {
            //parameter
            $schedule_id = $request->schedule_id;
            $visit_id = $request->visit_id;
            $this->interviewDetails->deleteAllInterview($schedule_id, $visit_id);
            DB::commit();
            return $this->responseNoContent(__('interviewdetails.delete'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     *  @OA\DELETE(
     *      path="/api/interview-details/delete-interviewer",
     *      operationId="DeleteInterviewer",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Delete Interviewer",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete Interviewer params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="number",
     *                      description="schedule id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      description="Facility Short Name",
     *                      default="堀江保健福祉センター分館（札幌市中央区）"
     *                  ),
     *                  @OA\Property(
     *                      property="visit_id",
     *                      type="number",
     *                      description="Visit Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="call_id",
     *                      type="string",
     *                      description="Call Id",
     *                      default="1584"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="Delete successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "204",
     *                       "message": "正常に削除しました。",
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
    public function deleteInterviewer(DeleteInterviewerRequest $request)
    {
        DB::beginTransaction();
        try {
            //parameter
            $schedule_id = $request->schedule_id;
            $facility_short_name = $request->facility_short_name;
            $visit_id = $request->visit_id;
            $call_id = $request->call_id;
            $this->interviewDetails->deleteInterviewer($schedule_id, $facility_short_name, $visit_id, $call_id);
            DB::commit();
            return $this->responseNoContent(__('interviewdetails.delete'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     *  @OA\GET(
     *      path="/api/interview-details/internal-schedule",
     *      operationId="getInternalSchedule",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Get Internal Schedule",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                           "status": "200",
     *                           "message": "success",
     *                           "data": {
     *                                   "office_schedule_type": "20",
     *                                   "title": "TEST",
     *                                   "start_date": "2021/12/06",
     *                                   "start_time": "2021/12/06 11:00:00",
     *                                   "end_time": "2021/12/06 13:00:00",
     *                                   "all_day_flag": 0,
     *                                   "remarks": null,
     *                                   "user_cd" : ""
     *                               }
     *                       }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getInternalSchedule(GetInternalScheduleRequest $request)
    {
        $schedule_id = $request->schedule_id;
        $data = $this->interviewDetails->getInternalSchedule($schedule_id);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\POST(
     *      path="/api/interview-details/save-internal-schedule",
     *      operationId="saveInternalSchedule",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Save Internal Schedule",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Save Internal Schedule Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="Schedule Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      description="Title",
     *                      default=" 説明会"
     *                  ),
     *                  @OA\Property(
     *                      property="office_schedule_type",
     *                      type="string",
     *                      description="Office Schedule Type",
     *                      default="20"
     *                  ),
     *                  @OA\Property(
     *                      property="office_schedule_type_old",
     *                      type="string",
     *                      description="Office Schedule Type Old",
     *                      default="20"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="date",
     *                      description="Start Date",
     *                      default="2021-12-06"
     *                  ),
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="date",
     *                      description="Start Time",
     *                      default="2021-12-06 11:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="date",
     *                      description="End Time",
     *                      default="2021-12-06 13:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="all_day_flag",
     *                      type="number",
     *                      description="All Day Flag",
     *                      default="2021-12-06 13:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="remarks",
     *                      type="string",
     *                      description="Remarks",
     *                      default="ABC"
     *                  ),
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
    public function saveInternalSchedule(SaveInternalScheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            //parameter
            $schedule_id = $request->schedule_id;
            $title = $request->title;
            $office_schedule_type = $request->office_schedule_type;
            $start_date = $request->start_date;
            $start_date_old = $request->start_date_old ?? $start_date;
            $start_time = $request->start_time;
            $end_time = $request->end_time;
            $all_day_flag = $request->all_day_flag;
            $remarks = $request->remarks;
            $user_cd_login = $this->getCurrentUser();
            $result = $this->interviewDetails->saveInternalSchedule($schedule_id, $title, $office_schedule_type, $start_date, $start_time, $end_time, $all_day_flag, $remarks,$user_cd_login,$start_date_old);
            if(!$result){
                return $this->responseErrorValidate(__('interviewdetails.select_date_report'));
            }
            DB::commit();
            return $this->responseCreated(__('interviewdetails.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     *  @OA\Delete(
     *      path="/api/interview-details/delete-internal-schedule",
     *      operationId="deleteInternalSchedule",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Delete Internal Schedule",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete Internal Schedule Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="schedule id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="office_schedule_id",
     *                      type="number",
     *                      description="office schedule id",
     *                      default="3"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="Delete successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "204",
     *                       "message": "正常に削除しました。",
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
    public function deleteInternalSchedule(DeleteInternalScheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            $schedule_id = $request->schedule_id;
            $office_schedule_id = $request->office_schedule_id;
            $result = $this->interviewDetails->deleteInternalSchedule($schedule_id, $office_schedule_id);
            if(!$result){
                return $this->responseErrorValidate(__('interviewdetails.delete_report', ['attribute' => '社内予定' ]));
            }
            DB::commit();
            return $this->responseNoContent(__('interviewdetails.delete'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }


    /**
     *  @OA\GET(
     *      path="/api/interview-details/private-schedule",
     *      operationId="getPrivateSchedule",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Get Private Schedule",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Response successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                           "status": "200",
     *                           "message": "success",
     *                           "data": {
     *                               {
     *                                   "title": "ABC",
     *                                   "start_date": "2021-12-07",
     *                                   "start_time": "2021-12-07 09:00:00",
     *                                   "end_time": "2021-12-07 11:30:00",
     *                                   "all_day_flag": 0,
     *                                   "remarks": "TEST",
     *                                   "user_cd" : "10002"
     *                               }
     *                           }
     *                       }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function getPrivateSchedule(GetPrivateScheduleRequest $request)
    {
        $schedule_id = $request->schedule_id;
        $data = $this->interviewDetails->getPrivateSchedule($schedule_id);
        return $this->responseSuccess('success', $data);
    }

    /**
     *  @OA\POST(
     *      path="/api/interview-details/save-private-schedule",
     *      operationId="savePrivateSchedule",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Save Private Schedule",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Save Private Schedule Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="Schedule Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                      description="Title",
     *                      default=" 説明会"
     *                  ),
     *                  @OA\Property(
     *                      property="start_date",
     *                      type="date",
     *                      description="Start Date",
     *                      default="2021-12-06"
     *                  ),
     *                  @OA\Property(
     *                      property="start_time",
     *                      type="date",
     *                      description="Start Time",
     *                      default="2021-12-06 11:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="end_time",
     *                      type="date",
     *                      description="End Time",
     *                      default="2021-12-06 13:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="all_day_flag",
     *                      type="number",
     *                      description="All Day Flag",
     *                      default="2021-12-06 13:00:00"
     *                  ),
     *                  @OA\Property(
     *                      property="remarks",
     *                      type="string",
     *                      description="Remarks",
     *                      default="ABC"
     *                  ),
     *                  @OA\Property(
     *                      property="private_id",
     *                      type="number",
     *                      description="Private Id",
     *                      default="1"
     *                  ),
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
    public function savePrivateSchedule(SavePrivateScheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            $schedule_id = $request->schedule_id;
            $title = $request->title;
            $start_date = $request->start_date;
            $start_time = $request->start_time;
            $end_time = $request->end_time;
            $all_day_flag = $request->all_day_flag;
            $remarks = $request->remarks;

            $this->interviewDetails->savePrivateSchedule($schedule_id, $title, $start_date, $start_time, $end_time, $all_day_flag, $remarks);
            DB::commit();
            return $this->responseCreated(__('interviewdetails.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     *  @OA\Delete(
     *      path="/api/interview-details/delete-private-schedule",
     *      operationId="deletePrivateSchedule",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Delete Private Schedule",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Delete Private Schedule Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="schedule id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="private_id",
     *                      type="number",
     *                      description="private id",
     *                      default="3"
     *                  )
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="Delete successfully",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                       "status": "204",
     *                       "message": "正常に削除しました。",
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
    public function deletePrivateSchedule(DeletePrivateScheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            $schedule_id = $request->schedule_id;
            $this->interviewDetails->deletePrivateSchedule($schedule_id);
            DB::commit();
            return $this->responseNoContent(__('interviewdetails.delete'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/interview-details/add-interview-destination",
     *      operationId="addInterviewDestination",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Add Interview Destination",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Add Interview Destination Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="visit_id",
     *                      type="number",
     *                      description="Visit Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      description="Person Cd",
     *                      default=" 01101013"
     *                  ),
     *                  @OA\Property(
     *                      property="person_name",
     *                      type="string",
     *                      description="Person Name",
     *                      default="及川 照和"
     *                  ),
     *                  @OA\Property(
     *                      property="department_cd",
     *                      type="string",
     *                      description="Department Cd",
     *                      default="9999"
     *                  ),
     *                  @OA\Property(
     *                      property="department_name",
     *                      type="string",
     *                      description="Department Name",
     *                      default="その他"
     *                  ),
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="number",
     *                      description="Schedule Id",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="facility_short_name",
     *                      type="string",
     *                      description="Facility Short Name",
     *                      default=""
     *                  ),
     *                  @OA\Property(
     *                      property="facility_cd",
     *                      type="string",
     *                      description="facility cd",
     *                      default="011010014"
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
    public function addInterviewDestination(AddInterviewDestinationRequest $request)
    {
        DB::beginTransaction();
        try {
            $visit_id = $request->visit_id;
            $person = $request->person;
            $schedule_id = $request->schedule_id;
            $facility_short_name = $request->facility_short_name;
            $facility_cd = $request->facility_cd;
            $date_system = date_create($this->systemParameterService->getCurrentSystemDateTime())->format('Y-m-d');
            $this->interviewDetails->addInterviewDestination($facility_cd, $visit_id, $person, $schedule_id, $facility_short_name, $date_system);
            DB::commit();
            return $this->responseCreated(__('interviewdetails.save'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/interview-details/check-person-cd",
     *      operationId="checkPersonCd",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Check Person Cd",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Add Interview Destination Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="visit_id",
     *                      type="string",
     *                      description="Stock Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="Schedule Id",
     *                      default=" 01101013"
     *                  ),
     *                  @OA\Property(
     *                      property="dataStock",
     *                      type="string",
     *                      description="Data Stock",
     *                      default="[]"
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
    public function checkPersonCd(CheckPersonCdRequest $request)
    {
        $result['message'] = '';
        $dataStock = $request->dataStock;
        $visit_id = $request->visit_id;
        $data = $this->interviewDetails->checkPersonCd($visit_id, $dataStock);
        if ($data > 0) {
            $result['message'] = __('interviewdetails.check_person_cd');
        }
        return $this->responseSuccess('success', $result);
    }

    /**
     *  @OA\POST(
     *      path="/api/interview-details/add-stock",
     *      operationId="addStockInterviewDetails",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Add Stock",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Add Stock Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="visit_id",
     *                      type="nmber",
     *                      description="Visit Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="Schedule Id",
     *                      default=" 2"
     *                  ),
     *                  @OA\Property(
     *                      property="stock",
     *                      type="string",
     *                      description="Stock",
     *                      default="[]"
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
    public function addStock(AddStockRequest $request)
    {
        try {
            DB::beginTransaction();
            //get info user cd
            $user_cd =  $this->getCurrentUser();
            $data_user = $this->auth->getInfoUser($user_cd);

            $visit_id = $request->visit_id;
            $schedule_id = $request->schedule_id;
            $stock = $request->stock ?? [];
            $date_system = date_create($this->systemParameterService->getCurrentSystemDateTime())->format('Y-m-d');

            $this->interviewDetails->addStock($visit_id, $schedule_id, $stock, $data_user, $date_system);
            DB::commit();
            return $this->responseCreated(__('interviewdetails.save'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/interview-details/create-plan-schedule",
     *      operationId="createPlanSchedule",
     *      tags={"A01-S03"},
     *      summary="Interview Details",
     *      description="Create Plan Schedule",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Create Plan Schedule Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="visit_id",
     *                      type="nmber",
     *                      description="Visit Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="Schedule Id",
     *                      default=" 2"
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
     *                       "data": {
     *                              "schedule_id": 2570,
     *                         }
     *                   }
     *              )
     *        ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
     */
    public function createPlanSchedule(CreatePlanScheduleRequest $request)
    {
        DB::beginTransaction();
        try {
            $schedule_id = $request->schedule_id;
            $visit_id = $request->visit_id;
            //get info user cd
            $user_cd =  $this->getCurrentUser();
            $data_user = $this->auth->getInfoUser($user_cd);
            $schedule_info = $this->interviewDetails->getSchedule($schedule_id);
            $data_user->schedule_user = $this->auth->getInfoUser($schedule_info->user_cd);
            $data_new = $this->interviewDetails->createPlanSchedule($visit_id, $schedule_id, $data_user);

            DB::commit();
            return $this->responseCreated(__('interviewdetails.copy'), ['schedule_id' => $data_new->schedule_id]);
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetails.system_error'));
        }
    }
}
