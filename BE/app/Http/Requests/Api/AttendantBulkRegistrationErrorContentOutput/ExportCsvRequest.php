<?php

namespace App\Http\Requests\Api\AttendantBulkRegistrationErrorContentOutput;

use App\Http\Requests\Api\ApiBaseRequest;

class ExportCsvRequest extends ApiBaseRequest {
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
                "table" => "t_convention",
                "condition" => [
                    "convention_id" => $this->convention_id
                ]
            ]
        ];

        $this->validateInputParamater($param);
        
        return [
            'json_file_name' => 'required',
            'convention_id' => 'required'
        ];
    }

    public function messages() {
        return [
            'convention_id' => [
                'required' => __('validation.required'),
            ],
        ];
    }
    public function attributes(){
        return [
            'json_file_name' => 'ファイル選択',
        ];
    }
}
