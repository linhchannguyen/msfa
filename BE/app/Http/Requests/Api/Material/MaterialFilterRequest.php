<?php

namespace App\Http\Requests\Api\Material;

use App\Http\Requests\Api\ApiBaseRequest;

class MaterialFilterRequest extends ApiBaseRequest {
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
            'document_type' => 'nullable|max:100',
            'product_cd' => 'max:10',
            'medical_subjects_group_cd' => 'max:5',
            'safety_information_flag' => 'max:1',
            'off_label_information_flag' => 'max:1',
            'date' => 'nullable|date',
            'document_name' => 'max:50',
        ];
    }

    public function messages() {
        return [
            'document_type' => [
                'max' => __('validation.max'),
            ],
            'product_cd' => [
                'max' => __('validation.max'),
            ],
            'medical_subjects_group_cd' => [
                'max' => __('validation.max'),
            ],
            'safety_information_flag' => [
                'max' => __('validation.max'),
            ],
            'off_label_information_flag' => [
                'max' => __('validation.max'),
            ],
            'date' => [
                'date' => __('validation.date')
            ],
            'document_name' => [
                'max' => __('validation.max'),
            ]
        ];
    }
}
