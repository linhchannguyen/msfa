<?php

namespace App\Http\Requests\Api\AccountSetting;

use App\Http\Requests\Api\ApiBaseRequest;

class AccountInfoRequest extends ApiBaseRequest {
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
        ];
    }
}
