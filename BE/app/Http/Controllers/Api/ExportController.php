<?php

namespace App\Http\Controllers\Api;

use App\Exports\ListAttendee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AttendantManagement\SearchAttendantRequest;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Exports\ExportListAttendee;
use App\Repositories\AttendantManagement\AttendantManagementRepositoryInterface;
use Illuminate\Support\Facades\Log;
use App\Repositories\FacilityUser\FacilityUserRepositoryInterface;
use Carbon\Carbon;
use App\Traits\DateTimeTrait;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    use DateTimeTrait;
    protected $faUserRepository, $attendant_repo;
    public function __construct(FacilityUserRepositoryInterface $faUserRepository, AttendantManagementRepositoryInterface $attendant_repo)
    {
        $this->faUserRepository = $faUserRepository;
        $this->attendant_repo = $attendant_repo;
        $this->middleware('role.has:' . config('role.CONVENTION_IMPLEMENTER.CODE') . ',' . config('role.CONVENTION_ATTENDEE_MG.CODE') . ',' . config('role.SYS_MG.CODE'));
    }
    public function listAttendee(SearchAttendantRequest $request)
    {
        // get convention name
        $conventionName = $this->faUserRepository->getConventionName($request->get('convention_id'))->convention_name ?? "";
        try {
        $conditions = $request;
        $convention_start_date = $this->attendant_repo->getStartDateConventionSchedule($conditions->convention_id)->start_date;
        $lecture_follow_up = $this->attendant_repo->getLectureFollowUp()->parameter_value ?? 0;
        $convention_start_date_add_two_day = Carbon::parse($convention_start_date)->addDays($lecture_follow_up)->format('Y-m-d');
        $conditions->convention_start_date = $convention_start_date;
        $conditions->convention_start_date_add_two_day = $convention_start_date_add_two_day;
        $conditions->status_item_cd_200 = STATUS_ITEM_GUIDE;
        $conditions->status_item_cd_700 = STATUS_ITEM_ATTENDED;
        $conditions->unfollow = (int)$request->unfollow ?? 0;
        $conditions->attendance = (int)$request->attendance ?? 0;
        // $user_cds = @json_decode($request->user_cd, true);
        $request->user_cds = empty($request->user_cd) ? [] : $request->user_cd;
        // $conditions->user_cd = $user_cds;

        
        $datas = [];
        $datas = $this->faUserRepository->listAttendee($conditions);
        foreach ($datas as $data) {
            // $data->series_convention_200 = UN_LAST_TIME_INFORMATION;
            $data->series_convention_200 = "";
            // $data->series_convention_700 = UN_LAST_TIME_ATTENDED;
            $data->series_convention_700 = "";
            $data->convention_attendee_status_detail = empty($data->convention_attendee_status_detail) ? [] : json_decode($data->convention_attendee_status_detail, true);
            if (empty($data->convention_attendee_status_detail)) {
                $convention_status_item = $this->attendant_repo->getConventionStatusItem();
                foreach ($convention_status_item as $status_item) {
                    $data->convention_attendee_status_detail[] = [
                        "status_item_cd" => $status_item->status_item_cd,
                        "status_item_value" => $status_item->status_item_cd == 300 ? "00" : "0",
                    ];
                }
            }
            foreach ($data->convention_attendee_status_detail as $attendee_status) {
                if($attendee_status['status_item_cd'] == 300){
                    $data->definition_value_10 = ($attendee_status['status_item_value'] == 10) ? "o" : "";
                    $data->definition_value_20 = ($attendee_status['status_item_value'] == 20) ? "o" : "";
                    $data->definition_value_30 = ($attendee_status['status_item_value'] == 30) ? "o" : "";
                }else{
                    $status_item_cd = 'status_item_cd_' . $attendee_status['status_item_cd'];
                    $status_item_cd = 'status_item_cd_' . $attendee_status['status_item_cd'];
                    $data->$status_item_cd = ($attendee_status['status_item_value'] != 0) ? "o" : "";
                }
            }
            $data->follow_date = empty($data->follow_dates) ? [] : json_decode($data->follow_dates, true);
            $data->series_convention = empty($data->series_convention) ? [] : json_decode($data->series_convention, true);
            array_multisort(
                array_column($data->follow_date, 'start_date'),
                SORT_ASC,
                $data->follow_date
            );
            array_multisort(
                array_column($data->series_convention, 'start_date'),
                SORT_DESC,
                $data->series_convention
            );
            foreach ($data->series_convention as $serie) {
                if ($serie['start_date'] < $data->start_date && $data->convention_id != $serie['convention_id'] && $serie['convention_attendees'] == 1) {
                    if ($serie['status_item_value_200'] >= 1) {
                        $data->series_convention_200 = "o";
                    }
                    if ($serie['status_item_value_700'] >= 1) {
                        $data->series_convention_700 = "o";
                    }
                    break;
                }
            }
            if (!empty($data->follow_date)) {
                $data->follow_date = $this->responseDateTimeFormat($data->follow_date[0]['start_date'], 'Y-m-d');
            } else {
                $data->follow_date = "";
            }
            if($data->other_person_flag  == 1){
                unset($data->display_position_name);
            }else{
                unset($data->medical_staff_name);
            }
            unset($data->series_convention);
            unset($data->convention_id);
            unset($data->start_date);
            unset($data->convention_attendee_status_detail);
            unset($data->follow_dates);
            unset($data->other_person_flag);
        }
        return Excel::download(new ListAttendee($datas), '出席者一覧_' . $conventionName . '_' . Carbon::now()->format('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        } catch (\Throwable $th) {
            return Excel::download(new ListAttendee([]), '出席者一覧_' . $conventionName . '_' . Carbon::now()->format('Y-m-d') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }
    }
}
