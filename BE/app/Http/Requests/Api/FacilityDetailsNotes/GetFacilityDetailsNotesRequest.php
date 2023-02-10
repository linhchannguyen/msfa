<?php

namespace App\Http\Requests\Api\FacilityDetailsNotes;

use App\Http\Requests\Api\ApiBaseRequest;

class GetFacilityDetailsNotesRequest extends ApiBaseRequest
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
            'start_date' => 'nullable|date:Y/mm/dd',
            'end_date' => 'nullable|date:Y/mm/dd|after_or_equal:start_date',
        ];
    }

    public function messages()
    {
        return [
            'facility_cd' => [
                'required' => __('validation.required')
            ],
            'start_date' => [
                'date_format' => __('validation.date_format')
            ],
            'end_date' => [
                'date_format' => __('validation.date_format'),
                'after_or_equal' => __('validation.after_or_equal')
            ]
        ];
    }
    public function attributes(){
        return [
            'start_date' => '活動日付',
            'end_date' => '終了日付',
        ];
    }
}
