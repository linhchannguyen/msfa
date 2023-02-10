<?php

namespace App\Http\Requests\Api\DocumentSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class DocumentProfileRequest extends ApiBaseRequest {
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
                "table" => "t_document",
                "condition" => [
                    "document_id" => $this->document_id
                ]
            ]
        ];
        $this->validateInputParamater($param);
        return [
            'document_id' => 'required|numeric|digits_between:1,20'
        ];
    }

    public function messages() {
        return [
            'document_id' => [
                'required' => __('validation.required'),
                'numeric' => __('validation.numeric'),
                'digits_between' => __('validation.digits_between')
            ]
        ];
    }
}
