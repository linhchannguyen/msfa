<?php

namespace App\Http\Requests\Api\UserManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class UpdateUserRequest extends ApiBaseRequest {
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
            'user_cd' => 'required|exists:i_user,user_cd|max:15',
            'user_name' => 'required|max:50',
            'mail_address' => 'required|email|max:255|regex:/^\S*$/u',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'start_date_old' => 'required|date',
            'flag_change' => 'required',
            'account_lock_remarks' => 'nullable|max:200',
        ];
    }

    public function messages() {
        return [
            'user_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
                'exists' => __('validation.exists')
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'end_date' => [
                'date' => __('validation.date'),
            ],
            'start_date_old' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
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
            'flag_change' => [
                'required' => __('validation.required'),
            ],
            'account_lock_remarks' => [
                'max' => __('validation.max'),
            ],
            'mail_address.regex' => __('validation.email'),
        ];
    }
    public function attributes(){
        return [
            'start_date' => '適用期間',
            'user_name' => '氏名',
            'start_date_old' => '古い適用期間',
            'account_lock_remarks' => 'アカウントロック',
            'mail_address' => 'メールアドレス',
        ];
    }
}
