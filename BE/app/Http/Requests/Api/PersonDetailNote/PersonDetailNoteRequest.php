<?php

namespace App\Http\Requests\Api\PersonDetailNote;

use App\Http\Requests\Api\ApiBaseRequest;

class PersonDetailNoteRequest extends ApiBaseRequest
{
    protected $dateTimeFields = [
        "last_update_datetime_from" => 'Y-m-d',
        "last_update_datetime_to" => 'Y-m-d'
    ];
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
        return [
            'last_update_datetime_from' => 'nullable|date:Y-mm-dd',
            'last_update_datetime_to' => 'nullable|date:Y-mm-dd|after_or_equal:last_update_datetime_from'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
