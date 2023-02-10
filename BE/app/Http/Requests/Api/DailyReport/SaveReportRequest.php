<?php

namespace App\Http\Requests\Api\DailyReport;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveReportRequest extends ApiBaseRequest
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
            'report_detail' => 'required|array',
            'report_detail.*.report_id' => 'nullable|max:20',
            'report_detail.*.org_cd' => 'required|max:20',
            'report_detail.*.org_label' => 'required|max:30',
            'report_detail.*.report_period_end_date' => 'required|date:Y-mm-dd',
            'report_detail.*.report_period_start_date' => 'required|date:Y-mm-dd',
            'report_detail.*.report_status_type' => 'required|max:3',
            'report_detail.*.submission_remarks' => 'nullable',
            'report_detail.*.comment' => 'nullable|max:200',
            'list_schedule' => 'nullable|array',
            'list_schedule.*.schedule' => 'nullable|array',
            'list_schedule.*.schedule.*.report_detail_note' => 'nullable',
            'list_schedule.*.schedule.*.schedule_id' => 'required|max:20',
            'list_schedule.*.schedule.*.schedule_type' => 'required|max:3',
        ];
    }

    public function messages()
    {
        return [
            'report_detail' => [
                'required' => __('validation.required'),
                'array' => __('validation.array'),
            ],
            'report_detail.*.report_id' => [
                'max' => __('validation.max'),
            ],
            'report_detail.*.org_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'report_detail.*.org_label' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'report_detail.*.report_period_end_date' => [
                'required' => __('validation.required'),
                'date_format' => __('validation.date_format'),
            ],
            'report_detail.*.report_period_start_date' => [
                'required' => __('validation.required'),
                'date_format' => __('validation.date_format'),
            ],
            'report_detail.*.report_status_type' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'report_detail.*.comment' => [
                'max' => __('validation.max'),
            ],
            'list_schedule' => [
                'array' => __('validation.array'),
            ],
            'list_schedule.*.schedule_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'list_schedule.*.schedule_type' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ]
        ];
    }
}
