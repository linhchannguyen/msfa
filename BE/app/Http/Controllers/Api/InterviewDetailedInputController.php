<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InterviewDetailedInput\GetInterviewDetailedInputRequest;
use App\Http\Requests\Api\InterviewDetailedInput\SavePlanRequest;
use App\Http\Requests\Api\InterviewDetailedInput\SaveRequest;
use App\Http\Requests\Api\InterviewDetails\GetInternalScheduleRequest;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Traits\DateTimeTrait;
use App\Services\AuthService;
use App\Services\InterviewDetailedInputService;

class InterviewDetailedInputController extends Controller
{
    use DateTimeTrait;
    private $interviewDetailedInput, $auth;

    public function __construct(InterviewDetailedInputService $interviewDetailedInput, AuthService $auth)
    {
        $this->interviewDetailedInput = $interviewDetailedInput;
        $this->auth = $auth;
        $this->middleware('role.not:' . config('role.DEV.CODE'));
    }

    /**
     * @OA\GET(
     *      path="/api/interview-detailed-input/get-screen-data",
     *      operationId="getScreenDataInterviewDetailedInput",
     *      tags={"A01-S04"},
     *      summary="Interview Detailed Input",
     *      description="Get Screen Data",
     *      security={{"jwt": {}}},
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *      type="object",
     *      example={
     *                  "status": "200",
     *                  "message": "success",
     *                  "data": {
     *                      "interviewContent": {
     *                          {
     *                              "content_cd": "10",
     *                              "content_name": "情報提供"
     *                          },
     *                          {
     *                              "content_cd": "100",
     *                              "content_name": "郵送"
     *                          }
     *                      },
     *                      "reaction": {
     *                          {
     *                              "reaction_cd": "10",
     *                              "reaction_name": "興味あり"
     *                          }
     *                      },
     *                      "phase": {
     *                          {
     *                              "phase_cd": "10",
     *                              "phase_name": "認知"
     *                          }
     *                      },
     *                      "m_variable_definition": {
     *                            "definition_label": "面談",
     *                            "definition_value": "0"
     *                       }
     *                  }
     *              }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getScreenData()
    {
        $data = $this->interviewDetailedInput->getScreenData();
        return $this->responseSuccess('success', $data);
    }

    /**
     * @OA\GET(
     *      path="/api/interview-detailed-input",
     *      operationId="getInterviewDetailedInput",
     *      tags={"A01-S04"},
     *      summary="Interview Detailed Input",
     *      description="Get Interview Detailed Input",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="call_id",in="query", @OA\Schema(type="string")),
     *      @OA\Parameter(name="schedule_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *      type="object",
     *      example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       "interviewDateTime": {
     *                           "start_date": "2021/12/06",
     *                           "start_time": "2021/12/06 11:50:00",
     *                           "end_time": "2021/12/06 13:40:00",
     *                           "facility_short_name": "田口医師会保健医療センター（札幌市中央区）",
     *                           "facility_cd": "011010003",
     *                           "person_name": "成田 裕子",
     *                           "person_cd": "01101003",
     *                           "call_id": 2,
     *                           "off_label_flag": 0,
     *                           "visit_id": 2,
     *                           "report_id": 258,
     *                           "report_status_type": "未作成"
     *                       },
     *                       "offLabel": {
     *                           "off_label_concent_flag": 0,
     *                           "related_product": "NULL",
     *                           "question": "NULL",
     *                           "answer": "NULL",
     *                           "re_question": "NULL",
     *                           "literature": "NULL",
     *                           "off_label_call_time": null
     *                       },
     *                       "detailArea": {
     *                           {
     *                               "detail_order": 2,
     *                               "content_name_tran": "郵送",
     *                               "product_name": "AAA製品100mg",
     *                               "detail_id": 2
     *                           }
     *                       }
     *                   }
     *               }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getInterviewDetailedInput(GetInterviewDetailedInputRequest $request)
    {
        $call_id = $request->call_id;
        $schedule_id = $request->schedule_id;
        $data = $this->interviewDetailedInput->getInterviewDetailedInput($call_id, $schedule_id);
        return $this->responseSuccess('success', $data);
    }

    public function checkInterviewDetailedInput(GetInterviewDetailedInputRequest $request)
    {
        return $this->responseSuccess('success', []);
    }

    /**
     * @OA\GET(
     *      path="/api/interview-detailed-input/get-detail-area",
     *      operationId="getDetailArea",
     *      tags={"A01-S04"},
     *      summary="Interview Detailed Input",
     *      description="Get Detail Area",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="detail_id",in="query", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="Successful response",
     *      @OA\JsonContent(
     *      type="object",
     *      example={
     *                   "status": "200",
     *                   "message": "success",
     *                   "data": {
     *                       "detail_order": 2,
     *                       "content_cd": "20",
     *                       "content_name_tran": "郵送",
     *                       "product_cd": "AAA-100",
     *                       "product_name": "AAA製品100mg",
     *                       "detail_id": 2,
     *                       "reaction_cd": "20",
     *                       "phase_cd": "20",
     *                       "prescription_count": null,
     *                       "note": "",
     *                       "remarks": "",
     *                       "materials": {
     *                           {
     *                               "document_id": "2",
     *                               "document_name": "test",
     *                               "org_label": "2",
     *                               "start_date": "2021-10-01",
     *                               "end_date": "2021-12-01",
     *                               "detail_id": 2,
     *                               "delete_flag": "0",
     *                               "change_flag": "0"
     *                           }
     *                       }
     *                   }
     *               }
     *          )
     *      ),
     *      @OA\Response(response=403, description="Forbidden Response")
     * )
     */
    public function getDetailArea(GetInternalScheduleRequest $request)
    {
        $detail_id = $request->detail_id;
        $data = $this->interviewDetailedInput->getDetailArea($detail_id);
        return $this->responseSuccess('success', $data);
    }


    /**
     *  @OA\POST(
     *      path="/api/interview-detailed-input/save-plan",
     *      operationId="savePlan",
     *      tags={"A01-S04"},
     *      summary="Interview Details",
     *      description="Save Plan",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Save Plan Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="call_id",
     *                      type="string",
     *                      description="Call Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="string",
     *                      description="Schedule Id",
     *                      default="10"
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
    public function savePlan(SavePlanRequest $request)
    {
        DB::beginTransaction();
        try {
            //parameter
            $call_id = $request->call_id;
            $schedule_id = $request->schedule_id;
            $this->interviewDetailedInput->savePlan($schedule_id, $call_id);
            DB::commit();
            return $this->responseCreated(__('interviewdetailedinput.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetailedinput.system_error'));
        }
    }

    /**
     *  @OA\POST(
     *      path="/api/interview-detailed-input/save",
     *      operationId="save",
     *      tags={"A01-S04"},
     *      summary="Interview Details",
     *      description="Save",
     *      security={{"jwt": {}}},
     *      @OA\RequestBody(
     *          description="Save Params",
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(
     *                      property="schedule_id",
     *                      type="number",
     *                      description="Schedule Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="call_id",
     *                      type="number",
     *                      description="Call Id",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="person_cd",
     *                      type="string",
     *                      description="person cd",
     *                      default="2"
     *                  ),
     *                  @OA\Property(
     *                      property="off_label_flag",
     *                      type="string",
     *                      description="Off Label Flag",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="off_label_concent_flag",
     *                      type="string",
     *                      description="Off Label Concent Flag",
     *                      default="1"
     *                  ),
     *                  @OA\Property(
     *                      property="off_label_call_time",
     *                      type="string",
     *                      description="Off Label Call Time",
     *                      default="120"
     *                  ),
     *                  @OA\Property(
     *                      property="related_product",
     *                      type="string",
     *                      description="Related Product",
     *                      default="related_product"
     *                  ),
     *                  @OA\Property(
     *                      property="question",
     *                      type="string",
     *                      description="Question",
     *                      default="Question"
     *                  ),
     *                  @OA\Property(
     *                      property="answer",
     *                      type="string",
     *                      description="Answer",
     *                      default="answer"
     *                  ),
     *                  @OA\Property(
     *                      property="re_question",
     *                      type="string",
     *                      description="Re Question",
     *                      default="re_question"
     *                  ),
     *                  @OA\Property(
     *                      property="literature",
     *                      type="string",
     *                      description="literature",
     *                      default="literature"
     *                  ),
     *                  @OA\Property(
     *                      property="detailArea",
     *                      type="string",
     *                      description="detailArea",
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
    public function save(SaveRequest $request)
    {
        DB::beginTransaction();
        try {
            $facility_cd = $request->facility_cd;
            $start_date = $request->start_date;
            $start_time = $request->start_time;
            $person_cd = $request->person_cd;
            $person_name = $request->person_name;
            $schedule_id = $request->schedule_id;
            //parameter
            $call_id = $request->call_id;

            //t_call
            $off_label_flag = $request->off_label_flag;
            $off_label_concent_flag = $request->off_label_concent_flag;
            $off_label_call_time = $request->off_label_call_time;
            $related_product = $request->related_product ?? null;
            $question = $request->question;
            $answer = $request->answer;
            $re_question = $request->re_question;
            $literature = $request->literature;

            $this->interviewDetailedInput->saveCall($schedule_id, $call_id, $person_cd, $off_label_flag, $off_label_concent_flag, $off_label_call_time, $related_product, $question, $answer, $re_question, $literature, $person_name);
            //detailArea
            $user_cd =  $this->getCurrentUser();

            $detailArea = $request->detailArea;
            $this->interviewDetailedInput->saveDetailArea($facility_cd, $person_cd, $start_date, $schedule_id, $call_id, $detailArea, $user_cd, $request->deleteArray, $start_time);

            DB::commit();
            return $this->responseCreated(__('interviewdetailedinput.update'));
        } catch (Exception $e) {
            //Error
            DB::rollBack();
            throw $e;
            return $this->responseSystemError(__('interviewdetailedinput.system_error'));
        }
    }
}
