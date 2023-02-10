<?php

namespace App\Http\Requests\Api\PersonDetailNetwork;

use App\Http\Requests\Api\ApiBaseRequest;

class PersonDetailNetworkRequest extends ApiBaseRequest
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
                    "person_cd" => $this->id
                ]
            ]
        ];
        $this->validateInputParamater($param);
        return [];
    }

    public function messages()
    {
        return [
        ];
    }
}
