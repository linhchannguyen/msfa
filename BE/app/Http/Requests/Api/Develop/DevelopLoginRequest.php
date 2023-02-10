<?php

namespace App\Http\Requests\Api\Develop;

use App\Http\Requests\Api\ApiBaseRequest;

class DevelopLoginRequest extends ApiBaseRequest {
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
            'developer_cd' => 'required',
            'password_hash' => 'required',
            'user_cd' => 'required'
        ];
        // |regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[a-zA-Z])[\x20-\x7E]{8,25}$/
    }

    public function messages() {
        return [
            'developer_cd' => [
                'required' => __('validation.required')
            ],
            'password_hash' => [
                'required' => __('validation.required'),
                // 'regex' => __('validation.regex'),
            ],
            'user_cd' => [
                'required' => __('validation.required')
            ],
        ];
    }

    public function attributes(){
        return [
            'developer_cd' => '開発者ID',
            'password_hash' => 'パスワード',
            'user_cd' => 'ユーザID',
        ];
    }

    
}
