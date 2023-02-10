<?php

namespace App\Http\Requests\Api\Region;

use App\Http\Requests\Api\ApiBaseRequest;

class RegionPrefectureRequest extends ApiBaseRequest {
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
            'region_cd' => 'nullable|string|max:3',
        ];
    }

    public function messages() {
        return [
            'region_cd' => [
                'max' => __('validation.max'),
            ]
        ];
    }
}
