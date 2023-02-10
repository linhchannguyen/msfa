<?php

namespace App\Http\Requests\Api\Region;

use App\Http\Requests\Api\ApiBaseRequest;

class RegionPrefectureMunicipalityRequest extends ApiBaseRequest {
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
            'prefecture_cd' => 'required|max:2',
            'municipality_name' => 'nullable|string|max:100',
        ];
    }

    public function messages() {
        return [
            'prefecture_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'municipality_name' => [
                'max' => __('validation.max'),
            ]
        ];
    }
}
