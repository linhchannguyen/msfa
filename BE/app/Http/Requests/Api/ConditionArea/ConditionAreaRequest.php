<?php

namespace App\Http\Requests\Api\ConditionArea;

use App\Http\Requests\Api\ApiBaseRequest;

class ConditionAreaRequest extends ApiBaseRequest {
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
            'facility_cd' => 'nullable|string',
            'select_group_id' => 'nullable|max:100',
            'target_product_cd' => 'nullable|string|max:10',
            'prefecture_cd' => 'nullable|string|max:2',
            'municipality_cd' => 'nullable|string|max:3',
            'facility_category_type' => 'nullable|string|max:2',
            'facility_name' => 'nullable|string|max:255',
            'user_cd' => 'nullable|string|max:15',
        ];
    }

    public function messages() {
        return [
            'target_product_cd' => [
                'max' => __('validation.max'),
            ],
            'prefecture_cd' => [
                'max' => __('validation.max'),
            ],
            'municipality_cd' => [
                'max' => __('validation.max'),
            ],
            'facility_category_type' => [
                'max' => __('validation.max'),
            ],
            'facility_name' => [
                'max' => __('validation.max'),
            ],
            'select_group_id' => [
                'max' => __('validation.max'),
            ],
            'user_cd' => [
                'max' => __('validation.max'),
            ],
        ];
    }
}
