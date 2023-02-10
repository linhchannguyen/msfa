<?php

namespace App\Http\Requests\Api\PermissionSetting;

use App\Http\Requests\Api\ApiBaseRequest;

class UpdatePermissionRequest extends ApiBaseRequest {

    protected $dateTimeFields = [
        "user_role.*.start_date" => 'Y-m-d',
        "user_role.*.end_date" => 'Y-m-d'
    ];
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
            'user_role' => 'required',
            'user_role.*.roles' => 'required',
            'user_role.*.start_date' => 'required|date:Y-mm-dd',
            'user_role.*.end_date' => 'nullable|date:Y-mm-dd'
        ];
    }

    public function messages() {
        return [
            'user_cd' => [
                'required' => __('validation.required'),
            ],
            'user_role' => [
                'required' => __('validation.required')
            ],
            'user_role.*.roles' => [
                'required' => __('validation.required')
            ],
            'user_role.*.start_date' => [
                'required' => __('validation.required'),
                'date_format' => __('validation.date_format')
            ],
            'user_role.*.end_date' => [
                'date_format' => __('validation.date_format')
            ],
        ];
    }
}
