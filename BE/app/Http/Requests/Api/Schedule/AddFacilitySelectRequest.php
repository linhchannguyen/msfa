<?php

namespace App\Http\Requests\Api\Schedule;

use App\Http\Requests\Api\ApiBaseRequest;

class AddFacilitySelectRequest extends ApiBaseRequest
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
            'display_option_type' => 'required|max:3',
            'all_day_flag' => 'required|max:1',
            'facility_cd' => 'nullable|max:25',
            'facility_short_name' => 'nullable|max:50',
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
