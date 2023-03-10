<?php

namespace App\Http\Requests\Api\DailyReport;

use App\Http\Requests\Api\ApiBaseRequest;

class ReportDetailRequest extends ApiBaseRequest
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
        if(!empty($this->report_id)){
            $param = [
                [
                    "table" => "t_report",
                    "condition" => [
                        "report_id" => $this->report_id
                    ]
                ]
            ];
            $this->validateInputParamater($param);
        }
       
        return [
            'report_period_start_date' => 'nullable|date',
            'report_period_end_date' => 'nullable|date',
            'report_id' => 'max:20',
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
            'report_id' => [
                'max' => __('validation.max')
            ],
        ];
    }
}
