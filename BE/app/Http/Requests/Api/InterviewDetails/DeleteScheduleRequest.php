<?php

namespace App\Http\Requests\Api\InterviewDetails;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class DeleteScheduleRequest extends ApiBaseRequest
{
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
            'schedule_id' => 'required|exists:t_schedule,schedule_id',
        ];

        // 'visit_id' => 'required|exists:t_visit,visit_id',
    }
    public function messages()
    {
        return [
            'schedule_id' => [
                'required' => __('validation.required'),
            ],
            // 'visit_id' => [
            //     'required' => __('validation.required'),
            // ],
        ];
    }
}
