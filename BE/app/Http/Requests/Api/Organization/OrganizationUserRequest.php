<?php

namespace App\Http\Requests\Api\Organization;

use App\Http\Requests\Api\ApiBaseRequest;

class OrganizationUserRequest extends ApiBaseRequest {
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
            'org_cd' => 'nullable|max:20',
        ];
    }

    public function messages() {
        return [
            'org_cd' => [
                'max' => __('validation.max'),
            ]
        ];
    }
}
