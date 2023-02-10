<?php

namespace App\Http\Requests\Api\FacilitySearch;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveSegmentTypeFacilitySearchRequest extends ApiBaseRequest {
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
            'facility_list' => 'required|array',
            'facility_list.*.facility_cd' => 'required|string:25',
            'facility_list.*.person_cd' => 'required|string:15',
            'facility_list.*.part_type' => 'required|integer',
            'facility_list.*.segment_list' => 'nullable|array',
            'facility_list.*.segment_list.*.segment_type' => 'nullable|string:3',
            'facility_list.*.segment_list.*.checked' => 'nullable|integer',
            'facility_list.*.segment_list.*.delete_flag' => 'nullable|integer',
        ];
    }

    public function messages() {
        return [
            'facility_list' => [
                'required' => __('validation.required'),
                'array' => __('validation.array')
            ],
            'facility_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'person_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'part_type' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'segment_list' => [
                'array' => __('validation.array')
            ],
            'segment_type' => [
                'max' => __('validation.max')
            ],
            'checked' => [
                'max' => __('validation.max')
            ],
            'delete_flag' => [
                'max' => __('validation.max')
            ]
        ];
    }
}
