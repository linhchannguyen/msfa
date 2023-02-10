<?php

namespace App\Http\Requests\Api\InFacilityTargetPersonManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class GetInFacilityTargetPersonManagementRequest extends ApiBaseRequest {
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
        ];
    }

    public function messages() {
        return [
            'facility_cd' => [
                'required' => __('validation.required'),
            ]
        ];
    }
}
