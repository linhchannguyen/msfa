<?php

namespace App\Http\Requests\Api\PermissionSetting;

use App\Http\Requests\Api\ApiBaseRequest;

class GetListPermissionSettingRequest extends ApiBaseRequest {
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
            'reference_date' => 'nullable|date',
        ];
    }

    public function messages() {
        return [
            'reference_date' => [
                'date' => __('validation.date'),
            ]
        ];
    }

    public function attributes(){
        return [
            'reference_date' => '基準日'
        ];
    }
}
