<?php

namespace App\Http\Requests\Api\FacilityDetailsTimeLine;

use App\Http\Requests\Api\ApiBaseRequest;

class FacilityDetailsTimeLineRequest extends ApiBaseRequest {
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
        $param = [
            [
                "table" => "m_facility",
                "condition" => [
                    "facility_cd" => $this->facility_cd
                ]
            ]
        ];

        $this->validateInputParamater($param);
        
        return [
            'facility_cd' => 'required',
            'start_datetime' => 'nullable|date:Y/mm/dd',
            'end_datetime' => 'nullable|date:Y/mm/dd|after_or_equal:start_datetime',
        ];
    }

    public function messages() {
        return [
            'facility_cd' => [
                'required' => __('validation.required'),
            ],
            'start_datetime' => [
                'date_format' => __('validation.date_format')
            ],
            'end_datetime' => [
                'date_format' => __('validation.date_format'),
                'after_or_equal' => __('validation.after_or_equal')
            ]
        ];
    }

    public function attributes()
    {
        return [
            'start_datetime' => '開始日時 ',
            'end_datetime' => '終了日時',
        ];
    }
}
