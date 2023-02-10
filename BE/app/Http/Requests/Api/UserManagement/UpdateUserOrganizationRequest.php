<?php

namespace App\Http\Requests\Api\UserManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class UpdateUserOrganizationRequest extends ApiBaseRequest {
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
            'start_date' => 'required|date',
            'start_date_old' => 'required|date',
            'end_date' => 'nullable|date',
            'organization' => 'nullable|array',
            'flag_change' => 'required',
            'organization.*.org_cd' => 'required',
            'organization.*.user_post_type' => 'required',
            'organization.*.main_flag' => 'required',
            'organization.*.delete_flag' => 'required',
        ];
    }

    public function messages() {
        return [
            'user_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'start_date_old' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'end_date' => [
                'date' => __('validation.date'),
            ],
            'organization' => [
                'array' => __('validation.array'),
            ],
            'flag_change' => [
                'required' => __('validation.required'),
            ],
            'organization.*.org_cd' => [
                'required' => __('validation.required'),
            ],
            'organization.*.user_post_type' => [
                'required' => __('validation.required'),
            ],
            'organization.*.main_flag' => [
                'required' => __('validation.required'),
            ],
            'organization.*.delete_flag' => [
                'required' => __('validation.required'),
            ],
        ];
    }

    public function attributes(){
        return [
            'start_date' => '適用開始日',
            'start_date_old' => '古い適用開始日',
            'organization.*.org_cd' => '所属組織',
            'organization.*.user_post_type' => '役職',
            'organization.*.main_flag' => '主所属',
            'organization.*.delete_flag' => '削除フラグ',
        ];
    }
}
