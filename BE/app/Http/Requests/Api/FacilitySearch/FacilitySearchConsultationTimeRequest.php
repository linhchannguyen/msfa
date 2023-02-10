<?php

namespace App\Http\Requests\Api\FacilitySearch;

use App\Http\Requests\Api\ApiBaseRequest;

class FacilitySearchConsultationTimeRequest extends ApiBaseRequest {
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
            'facility_cd' => 'required|max:25',
            'consultation_hours_note' => 'max:2000',
        ];
    }

    public function messages() {
        return [
            'facility_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'consultation_hours_note' => [
                'max' => __('validation.max')
            ]
        ];
    }
}
