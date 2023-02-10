<?php

namespace App\Http\Requests\Api\PostedUserManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class ValidateUserRequest extends ApiBaseRequest {
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
            'user_cd' => 'required|exists:m_user,user_cd',
        ];
    }

    public function messages() {
        return [
        ];
    }
}
