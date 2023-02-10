<?php

namespace App\Http\Requests\Api\DailyReport;

use App\Http\Requests\Api\ApiBaseRequest;

class VacationReportRequest extends ApiBaseRequest {
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
            'report_id' => 'required|max:20'
        ];
    }

    public function messages() {
        return [
            'report_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ]
        ];
    }
}
