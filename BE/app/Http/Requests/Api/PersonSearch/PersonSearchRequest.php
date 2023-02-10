<?php

namespace App\Http\Requests\Api\PersonSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class PersonSearchRequest extends ApiBaseRequest
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
                "table" => "m_person",
                "condition" => [
                    "person_cd" => $this->person_cd
                ]
            ]
        ];

        $this->validateInputParamater($param);
        
        return [
            'person_cd' => 'required|max:15',
        ];
    }

    public function messages()
    {
        return [
            'person_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ]
        ];
    }
}
