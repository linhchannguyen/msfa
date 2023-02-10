<?php

namespace App\Http\Requests\Api\BriefingSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class ApprovalRequest extends ApiBaseRequest {
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
            'briefing_id' => 'required|max:20',
            'status_type' => 'required|max:2',
            'comment' => 'nullable|max:200',
            'request_type' => 'required|max:3'
        ];
    }

    public function messages() {
        return [
            'briefing_id' => [
                'max' => __('validation.max'),
                'required' => __('validation.required')
            ],
            'status_type' => [
                'max' => __('validation.max'),
                'required' => __('validation.required')
            ],
            'request_type' => [
                'max' => __('validation.max'),
                'required' => __('validation.required')
            ],
            'comment' => [
                'max' => __('validation.max')
            ]
        ];
    }
}
