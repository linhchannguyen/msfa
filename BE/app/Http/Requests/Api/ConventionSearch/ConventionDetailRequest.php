<?php

namespace App\Http\Requests\Api\ConventionSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class ConventionDetailRequest extends ApiBaseRequest
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
        if (!empty($this->convention_id ?? '')) {
            $param = [
                [
                    "table" => "t_convention",
                    "condition" => [
                        "convention_id" => $this->convention_id
                    ]
                ]
            ];
            $this->validateInputParamater($param);
        }
        if (!empty($this->schedule_id ?? '')) {
            $param = [
                [
                    "table" => "t_convention",
                    "condition" => [
                        "schedule_id" => $this->schedule_id
                    ]
                ]
            ];
            $this->validateInputParamater($param);
        }
        return [
            'convention_id' => 'max:20',
            'schedule_id' => 'max:20',
        ];
    }

    public function messages()
    {
        return [
            'convention_id' => [
                'max' => __('validation.max')
            ],
            'schedule_id' => [
                'max' => __('validation.max')
            ]
        ];
    }
}
