<?php

namespace App\Http\Requests\Api\UserManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class CreateUserRequest extends ApiBaseRequest {
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
            'user_cd' => 'required|max:15',
            'user_name' => 'required|max:50',
            'mail_address' => 'required|email|max:255|regex:/^\S*$/u',
            'start_date' => 'required|date',
        ];
    }

    public function messages() {
        return [
            'user_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'user_name' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'mail_address' => [
                'required' => __('validation.required'),
                'email' => __('validation.email'),
                'max' => __('validation.max'),
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'mail_address.regex' => __('validation.email'),
        ];
    }

    public function attributes(){
        return [
            'user_cd' => 'ユーザコード',
            'user_name' => 'ユーザ名 ',
            'mail_address' => 'メールアドレス',
            'start_date' => '適用開始日',
        ];
    }
}
