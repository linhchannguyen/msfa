<?php

namespace App\Http\Requests\Api\AttendantCollectiveRegistration;

use App\Http\Requests\Api\ApiBaseRequest;

class ImportCsvRequest extends ApiBaseRequest {
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
            'convention_id' => 'required',
            'csv_file' => 'required|file|mimes:csv,txt,xls,xlsx',
        ];
    }

    public function messages() {
        return [
            'convention_id' => [
                'required' => __('validation.required'),
            ],
            'csv_file' => [
                'required' => __('validation.required'),
                'file' => __('validation.file'),
                'mimes' => __('validation.mimes'),
            ],
        ];
    }
    public function attributes(){
        return [
            'csv_file' => 'ファイル選択',
        ];
    }
}
