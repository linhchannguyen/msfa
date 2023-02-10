<?php

namespace App\Http\Requests\Api\Exports;

use App\Http\Requests\Api\ApiBaseRequest;

class ExportListAttendee extends ApiBaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_cd' => 'exists:m_user,user_cd'
        ];
    }
    /**
     * 
     */
    public function messages() {
        return [
            'user_cd' => [
                'exists' => __('validation.exists')
            ]
        ];
    }
    public function attributes(){
        return [
            'user_cd' => '確認依頼先'
        ];
    }
}
