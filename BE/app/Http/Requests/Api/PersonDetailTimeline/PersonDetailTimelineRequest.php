<?php

namespace App\Http\Requests\Api\PersonDetailTimeline;

use App\Http\Requests\Api\ApiBaseRequest;

class PersonDetailTimelineRequest extends ApiBaseRequest
{
    protected $dateTimeFields = [
        "start_datetime" => 'Y-m-d',
        "end_datetime" => 'Y-m-d'
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
                    "person_cd" => $this->person_cd
                ]
            ]
        ];

        $this->validateInputParamater($param);

        return [
            'person_cd' => 'required',
            'start_datetime' => 'nullable|date:Y-mm-dd',
            'end_datetime' => 'nullable|date:Y-mm-dd|after_or_equal:start_datetime'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
