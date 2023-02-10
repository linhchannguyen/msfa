<?php

namespace App\Http\Requests\Api\ConventionSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class ConventionAllRequest extends ApiBaseRequest {
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
            'convention_name' => 'nullable|max:100',
            'convention_id' => 'nullable|max:20',
            'convention_type' => 'nullable|max:2',
            'status_type' => 'nullable|max:2',
            'remand_flag' => 'required|max:1'
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
            'convention_name' => [
                'max' => __('validation.max')
            ],
            'convention_id' => [
                'max' => __('validation.max')
            ],
            'convention_type' => [
                'max' => __('validation.max')
            ],
            'status_type' => [
                'max' => __('validation.max')
            ],
            'remand_flag' => [
                'required' => __('validation.max'),
                'max' => __('validation.max')
            ]
        ];
    }
}
