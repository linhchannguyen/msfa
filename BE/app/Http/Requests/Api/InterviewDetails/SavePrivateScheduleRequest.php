<?php

namespace App\Http\Requests\Api\InterviewDetails;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class SavePrivateScheduleRequest extends ApiBaseRequest
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
            'title' => 'nullable|max:30',
            'schedule_id' => 'required',
            'start_date' => 'required|date',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'all_day_flag' => 'required',
            'remarks' => 'nullable|max:200',
        ];
    }
    public function messages()
    {
        return [
            'title' => [
                'max' => __('validation.max')
            ],
            'schedule_id' => [
                'required' => __('validation.required'),
                'exists' => __('validation.exists')
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'start_time' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'end_time' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'all_day_flag' => [
                'required' => __('validation.required')
            ],
            'remarks' => [
                'max' => __('validation.max')
            ]
        ];
    }
}
