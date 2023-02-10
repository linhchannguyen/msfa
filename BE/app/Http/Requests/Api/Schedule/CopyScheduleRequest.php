<?php

namespace App\Http\Requests\Api\Schedule;

use App\Http\Requests\Api\ApiBaseRequest;

class CopyScheduleRequest extends ApiBaseRequest
{
    protected $dateTimeFields = [
        "schedule_date_from" => 'Y-m-d',
        "schedule_date_to" => 'Y-m-d',
        "start_date" => 'Y-m-d'
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
            'start_date' => 'required|date:Y-mm-dd',
            'schedule_date_from' => 'required|date:Y-mm-dd',
            'schedule_date_to' => 'required|date:Y-mm-dd|after_or_equal:schedule_date_from'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
