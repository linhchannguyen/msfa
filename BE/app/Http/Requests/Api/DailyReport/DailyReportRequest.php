<?php

namespace App\Http\Requests\Api\DailyReport;

use App\Http\Requests\Api\ApiBaseRequest;

class DailyReportRequest extends ApiBaseRequest {
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
            'report_id' => 'required|max:20',
            'report_status_type' => 'required|max:3'
        ];
    }

    public function messages() {
        return [
            'report_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'report_status_type' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ]
        ];
    }
}
