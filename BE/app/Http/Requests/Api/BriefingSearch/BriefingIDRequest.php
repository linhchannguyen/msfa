<?php

namespace App\Http\Requests\Api\BriefingSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class BriefingIDRequest extends ApiBaseRequest {
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
            'briefing_id' => 'required|max:20'
        ];
    }

    public function messages() {
        return [
            'briefing_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ]
        ];
    }
}
