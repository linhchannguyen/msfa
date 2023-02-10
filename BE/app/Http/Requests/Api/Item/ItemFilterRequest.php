<?php

namespace App\Http\Requests\Api\Item;

use App\Http\Requests\Api\ApiBaseRequest;

class ItemFilterRequest extends ApiBaseRequest {
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
            'product_group_cd' => 'nullable|string|max:10',
            'definition_value' => 'required|max:100',
        ];
    }

    public function messages() {
        return [
            'product_group_cd' => [
                'max' => __('validation.max'),
            ],
            'definition_value' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
        ];
    }
}
