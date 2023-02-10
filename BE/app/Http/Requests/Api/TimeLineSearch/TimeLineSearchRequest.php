<?php

namespace App\Http\Requests\Api\TimeLineSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class TimeLineSearchRequest extends ApiBaseRequest {
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
            'start_datetime' => 'nullable|date',
            'end_datetime' => 'nullable|date'
        ];
    }

    public function messages() {
        return [
            'start_datetime' => [
                'date' => __('validation.date')
            ],
            'end_datetime' => [
                'date' => __('validation.date')
            ]
        ];
    }
}
