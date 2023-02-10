<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;

class ProxyUserSelectionRequest extends ApiBaseRequest {
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
            'user_cd' => 'required'
        ];
    }

    public function messages() {
        return [
            'user_cd' => [
                'required' => __('validation.required')
            ]
        ];
    }

    public function attributes(){
        return [
            'user_cd' => '代行ログインユーザ',
        ];
    }
}
