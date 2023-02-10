<?php

namespace App\Http\Requests\Api\Schedule;

use App\Http\Requests\Api\ApiBaseRequest;

class AddVisitRequest extends ApiBaseRequest
{
    protected $dateTimeFields = [
        "start_date" => 'Y-m-d',
        "start_time" => 'Y-m-d H:i:s',
        "end_time" => 'Y-m-d H:i:s'
    ];
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'schedule_type' => 'required|string|max:2',
            'start_date' => 'required|date:Y-mm-dd',
            'start_time' => 'required|date:Y-mm-dd H:i:s',
            'end_time' => 'required|date:Y-mm-dd H:i:s|after_or_equal:start_time',
            'all_day_flag' => 'required|max:1',
            'facility_cd' => 'nullable|max:25',
            'facility_short_name' => 'nullable|max:50',
            'person_cd' => 'required|max:15',
            'person_name' => 'required|max:50',
            'department_cd' => 'nullable|max:4',
            'department_name' => 'nullable|max:100',
            'display_position_name' => 'nullable|max:10'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}