<?php

namespace App\Http\Requests\Api\InterviewDetails;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class DeleteInterviewerRequest extends ApiBaseRequest
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
        return [
            'schedule_id' => 'required',
            'facility_short_name' => 'required',
            'visit_id' => 'required',
            'call_id' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'schedule_id' => [  
                'required' => __('validation.required'),
            ],
            'facility_short_name' => [
                'required' => __('validation.required')
            ],
            'visit_id' => [
                'required' => __('validation.required'),
            ],
            'call_id' => [
                'required' => __('validation.required'),
            ],
        ];
    }
}
