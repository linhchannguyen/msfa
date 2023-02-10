<?php

namespace App\Http\Requests\Api\InterviewDetails;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveDateTimeSettingRequest extends ApiBaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'schedule_id' => 'required',
            'start_date' => 'required|date',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'all_day_flag' => 'required|boolean',
            'visit_id' => 'required',
            'remarks' => 'nullable|max:200',
            'facility_short_name' => 'required',
            'accompanying_user' => 'array',
            'accompanying_user.*.user_cd' => 'required',
            'accompanying_user.*.user_name' => 'required',
            'accompanying_user.*.org_short_name' => 'required',
            'accompanying_user.*.org_cd' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'schedule_id' => [
                'required' => __('validation.required'),
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'start_time' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'end_time' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'all_day_flag' => [
                'required' => __('validation.required'),
                'boolean' => __('validation.boolean')
            ],
            'visit_id' => [
                'required' => __('validation.required'),
            ],
            'remarks' => [
                'max' => __('validation.max')
            ],
            'facility_short_name' => [
                'required' => __('validation.required'),
            ],
            'accompanying_user' => [
                'array' => __('validation.array')
            ],
            'accompanying_user.*.user_cd' => [
                'required' => __('validation.required')
            ],
            'accompanying_user.*.user_name' => [
                'required' => __('validation.required')
            ],
            'accompanying_user.*.org_short_name' => [
                'required' => __('validation.required')
            ],
            'accompanying_user.*.org_cd' => [
                'required' => __('validation.required')
            ],
        ];
    }

    public function attributes()
    {
        return [
            'accompanying_user' => '社内同行者',
            'accompanying_user.*.user_cd' => 'ユーザコード（同行者）',
            'accompanying_user.*.user_name' => 'ユーザ名（同行者当時） ',
            'accompanying_user.*.org_short_name' => '組織ラベル（同行者当時）',
            'accompanying_user.*.org_cd' => '組織コード（同行者当時）',
        ];
    }
}
