<?php

namespace App\Http\Requests\Api\BriefingSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class ListBriefingRequest extends ApiBaseRequest {
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
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'status_type' => 'nullable|max:2',
            'briefing_type' => 'nullable|max:2',
            'briefing_id' => 'nullable|max:20',
            'remand_flag' => 'required|max:1',
            'approval_flag' => 'required|max:1',
        ];
    }

    public function messages() {
        return [
            'start_date' => [
                'date' => __('validation.date')
            ],
            'end_date' => [
                'date' => __('validation.date')
            ],
            'status_type' => [
                'max' => __('validation.max')
            ],
            'briefing_type' => [
                'max' => __('validation.max')
            ],
            'briefing_id' => [
                'max' => __('validation.max')
            ],
            'remand_flag' => [
                'max' => __('validation.max'),
                'required' => __('validation.required')
            ],
            'approval_flag' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ]
        ];
    }
}
