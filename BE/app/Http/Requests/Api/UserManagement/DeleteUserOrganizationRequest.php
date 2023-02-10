<?php

namespace App\Http\Requests\Api\UserManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class DeleteUserOrganizationRequest extends ApiBaseRequest {
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
            'user_cd' => 'required|exists:i_user_org,user_cd',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ];
    }

    public function messages() {
        return [
            'user_cd' => [
                'required' => __('validation.required'),
                'exists' => __('validation.exists'),
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'end_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ]
        ];
    }
}
