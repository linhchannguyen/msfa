<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CalendarService;
use App\Services\SystemParameterService;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    private $service, $serviceDate, $date;

    public function __construct(CalendarService $service, SystemParameterService $serviceDate)
    {
        $this->service = $service;
        $this->serviceDate = $serviceDate;
        $this->date = $this->serviceDate->getCurrentSystemDateTime();
        $this->middleware('role.has:'.config('role.CALL_IMPLEMENTER.CODE').','.config('role.SYS_MG.CODE'))->only('getListData');

    }
    /**
     * @OA\GET(
     *      path="/api/calendar",
     *      operationId="getCalendarList",
     *      tags={"A03-S01"},
     *      summary="Get list daily report",
     *      description="",
     *      security={{"jwt": {}}},
     *      @OA\Parameter(name="startDate",in="query", @OA\Schema(type="date")),
     *      @OA\Parameter(name="endDate",in="query", @OA\Schema(type="date")),
     *      @OA\Response(response=200, description="Successful response",
     *             @OA\JsonContent(
     *              type="object",
     *              example={
     *                     "status": "200",
     *                     "data":{
     *
     *                     }
     *                 }
     *              )
     *        ),
     *      @OA\Response(response=500, description="External System Error"),
     *      @OA\Response(response=403, description="Forbidden Error")
     * )
     */
    public function getListData(Request $request)
    {
        $startDate = $request->startDate;
        $endDate = $request->endDate;
        if (empty($startDate)) {

            $startDate = date('Y-m-d', strtotime('monday this week', strtotime($this->date)));
            $startDate = date("Y-m-d", strtotime('-1 days', strtotime($startDate)));
        } else {
            $startDate = date('Y-m-d', strtotime('-7 days', strtotime($startDate)));
        }
        if (empty($endDate)) {

            $endDate = date('Y-m-d', strtotime('sunday this week', strtotime($this->date)));
            $endDate = date("Y-m-d", strtotime('-1 days', strtotime($endDate)));
        }
        $data = $this->service->getList($startDate, $endDate);
        return $this->responseSuccess('success', $data);
    }
    public function getListSchedule(Request $request)
    {
        $startDate = date("Y-m-d",strtotime($request->startDate));
        $endDate = date("Y-m-d",strtotime($request->endDate));
        $data = $this->service->getListByMonthYear( $startDate, $endDate);
        return $this->responseSuccess('success', $data);
    }
}
