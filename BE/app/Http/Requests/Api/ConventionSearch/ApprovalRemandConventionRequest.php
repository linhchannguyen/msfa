<?php

namespace App\Http\Requests\Api\ConventionSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class ApprovalRemandConventionRequest extends ApiBaseRequest {
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
            'convention_id' => 'required|max:20',
            'status_type' => 'required|max:2',
            'convention_name' => 'required|max:100',
            'comment' => 'nullable|max:200',
        ];
    }

    public function messages() {
        return [
            'convention_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'status_type' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'convention_name' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'comment' => [
                'max' => __('validation.max')
            ]
        ];
    }
}
