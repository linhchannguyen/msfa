<?php

namespace App\Http\Requests\Api\InterviewDetailedInput;

use App\Http\Requests\Api\ApiBaseRequest;

class SavePlanRequest extends ApiBaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'call_id' => 'required',
            'schedule_id' => 'required',
        ];
    }

    public function messages() {
        return [
            'call_id' => [
                'required' => __('validation.required'),
            ],
            'schedule_id' => [
                'required' => __('validation.required'),
            ]
        ];
    }
}
