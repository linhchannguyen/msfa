<?php

namespace App\Http\Requests\Api\InterviewDetails;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class AddInterviewDestinationRequest extends ApiBaseRequest
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
            'visit_id' => 'required',
            'person' => 'required|array',
            'facility_short_name' => 'required',
            'facility_cd' => 'required',
        ];
    }
}
