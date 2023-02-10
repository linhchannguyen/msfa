<?php

namespace App\Http\Requests\Api\BriefingSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class BriefingDetailRequest extends ApiBaseRequest {
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
    public function rules() 
    {
        if (!empty($this->briefing_id ?? '')) {
            $param = [
                [
                    "table" => "t_briefing",
                    "condition" => [
                        "briefing_id" => $this->briefing_id
                    ]
                ]
            ];
            $this->validateInputParamater($param);
        }
        if (!empty($this->schedule_id ?? '')) {
            $param = [
                [
                    "table" => "t_briefing",
                    "condition" => [
                        "schedule_id" => $this->schedule_id
                    ]
                ]
            ];
            $this->validateInputParamater($param);
        }
        return [
            'briefing_id' => 'nullable|max:20',
            'schedule_id' => 'nullable|max:20'
        ];
    }

    public function messages() {
        return [
            'briefing_id' => [
                'max' => __('validation.max')
            ],
            'schedule_id' => [
                'max' => __('validation.max')
            ]
        ];
    }
}
