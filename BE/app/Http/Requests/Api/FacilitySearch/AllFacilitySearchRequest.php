<?php

namespace App\Http\Requests\Api\FacilitySearch;

use App\Http\Requests\Api\ApiBaseRequest;

class AllFacilitySearchRequest extends ApiBaseRequest {
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
            'facility_cd' => 'nullable|max:25',
            'facility_short_name' => 'nullable|max:255',
            'phone' => 'nullable|max:15',
            'prefecture_cd' => 'nullable|max:2',
            'municipality_cd' => 'nullable|max:3',
            'ultmarc_delete_flag' => 'min:0|required|integer|max:1',
            'facility_consideration_options' => 'required|max:10',
        ];
    }

    public function messages() {
        return [
            'facility_cd' => [
                'max' => __('validation.max')
            ],
            'facility_short_name' => [
                'max' => __('validation.max')
            ],
            'phone' => [
                'max' => __('validation.max')
            ],
            'prefecture_cd' => [
                'max' => __('validation.max')
            ],
            'municipality_cd' => [
                'max' => __('validation.max')
            ],
            'ultmarc_delete_flag' => [
                'required' => __('validation.required'),
                'min' => __('validation.min'),
                'max' => __('validation.max'),
                'integer' => __('validation.integer')
            ],
            'facility_consideration_options' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'user_cd' => [
                'max' => __('validation.max')
            ],
        ];
    }
}
