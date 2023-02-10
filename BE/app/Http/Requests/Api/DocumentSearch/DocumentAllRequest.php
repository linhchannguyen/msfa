<?php

namespace App\Http\Requests\Api\DocumentSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class DocumentAllRequest extends ApiBaseRequest {
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
            'document_name' => 'nullable|max:50',
            'product_cd' => 'nullable|max:10',
            'disease' => 'nullable|max:50',
            'medical_subjects_group_cd' => 'nullable|max:5',
            'safety_information_flag' => 'nullable|max:1',
            'off_label_information_flag' => 'nullable|max:1',
            'date' => 'nullable|date'
        ];
    }

    public function messages() {
        return [
            'document_name' => [
                'max' => __('validation.max')
            ],
            'product_cd' => [
                'max' => __('validation.max')
            ],
            'disease' => [
                'max' => __('validation.max')
            ],
            'medical_subjects_group_cd' => [
                'max' => __('validation.max')
            ],
            'safety_information_flag' => [
                'max' => __('validation.max')
            ],
            'off_label_information_flag' => [
                'max' => __('validation.max')
            ],
            'date' => [
                'date' => __('validation.date')
            ]
        ];
    }
}
