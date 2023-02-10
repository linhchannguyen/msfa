<?php

namespace App\Http\Requests\Api\Facility;

use App\Http\Requests\Api\ApiBaseRequest;

class FacilityPersonalRequest extends ApiBaseRequest {
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
            'segment_type' => 'nullable|max:3',
            'person_name' => 'nullable|max:50',
        ];
    }

    public function messages() {
        return [
            'segment_type' => [
                'max' => __('validation.max'),
            ],
            'person_name' => [
                'max' => __('validation.max'),
            ],
        ];
    }
}
