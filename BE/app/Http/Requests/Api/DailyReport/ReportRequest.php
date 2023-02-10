<?php

namespace App\Http\Requests\Api\DailyReport;

use App\Http\Requests\Api\ApiBaseRequest;

class ReportRequest extends ApiBaseRequest
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
            'report_period_start_date' => 'required|date',
            'report_period_end_date' => 'nullable|date',
            'org_cd' => 'required|max:20',
            'mode_week' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [
            'report_period_start_date' => [
                'date' => __('validation.date')
            ],
            'report_period_end_date' => [
                'date' => __('validation.date')
            ],
            'org_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'mode_week' => [
                'required' => __('validation.required'),
                'boolean' => __('validation.boolean')
            ],
        ];
    }
}
