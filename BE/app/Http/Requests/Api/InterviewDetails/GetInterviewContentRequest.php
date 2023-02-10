<?php

namespace App\Http\Requests\Api\InterviewDetails;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class GetInterviewContentRequest extends ApiBaseRequest
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
                "table" => "t_schedule",
                "condition" => [
                    "schedule_id" => $this->schedule_id
                ]
            ]
        ];
        
        $this->validateInputParamater($param);

        return [
            'schedule_id' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'schedule_id' => [
                'required' => __('validation.required'),
            ]
        ];
    }
}
