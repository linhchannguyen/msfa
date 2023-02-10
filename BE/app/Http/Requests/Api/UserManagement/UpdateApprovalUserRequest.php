<?php

namespace App\Http\Requests\Api\UserManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class UpdateApprovalUserRequest extends ApiBaseRequest {
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
            'approval_work_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'start_date_old' => 'required|date',
            'approval_layer_num' => 'nullable|array',
            'flag_change' => 'required',
            'approval_layer_num.*.approval_layer_num' => 'required',
            'approval_layer_num.*.approval_user' => 'nullable|array',
            'approval_layer_num.*.approval_user.*.approval_user_cd' => 'required',
            'approval_layer_num.*.approval_user.*.user_name' => 'required',
            'approval_layer_num.*.approval_user.*.org_cd' => 'required',
            'approval_layer_num.*.approval_user.*.delete_flag' => 'required',
        ];
    }

    public function messages() {
        return [
            'user_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'approval_work_type' => [
                'required' => __('validation.required')
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'end_date' => [
                'date' => __('validation.date')
            ],
            'start_date_old' => [
                'date' => __('validation.date')
            ],
            'approval_layer_num' => [
                'array' => __('validation.array')
            ],
            'flag_change' => [
                'required' => __('validation.required')
            ],
            'approval_layer_num.*.approval_layer_num' => [
                'required' => __('validation.required')
            ],
            'approval_layer_num.*.approval_user' => [
                'array' => __('validation.array')
            ],
            'approval_layer_num.*.approval_user.*.approval_user_cd' => [
                'required' => __('validation.required')
            ],
            'approval_layer_num.*.approval_user.*.user_name' => [
                'required' => __('validation.required')
            ],
            'approval_layer_num.*.approval_user.*.org_cd' => [
                'required' => __('validation.required')
            ],
            'approval_layer_num.*.approval_user.*.delete_flag' => [
                'required' => __('validation.required')
            ],
        ];
    }

    public function attributes(){
        return [
            'approval_layer_num.*.approval_layer_num' => '承認階層',
            'approval_layer_num.*.approval_user' => 'ユーザ（承認者）',
            'approval_layer_num.*.approval_user.*.approval_user_cd' => 'ユーザコード（承認者） ',
            'approval_layer_num.*.approval_user.*.user_name' => 'ユーザ名 ',
            'approval_layer_num.*.approval_user.*.org_cd' => '組織コード',
            'approval_layer_num.*.approval_user.*.delete_flag' => '削除フラグ',
        ];
    }
}
