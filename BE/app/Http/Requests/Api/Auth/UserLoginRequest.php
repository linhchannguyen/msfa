<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;

class UserLoginRequest extends ApiBaseRequest {
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
            'user_cd' => 'required',
            'password_hash' => 'required'
        ];
        //|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[a-zA-Z])[\x20-\x7E]{8,25}$/
    }

    public function messages() {
        return [
            'user_cd' => [
                'required' => __('validation.required'),
            ],
            'password_hash' => [
                'required' => __('validation.required'),
                
            ],
        ];
    }
    // 'regex' => __('validation.regex'),

    public function attributes(){
        return [
            'user_cd' => 'ログインID',
        ];
    }
}
