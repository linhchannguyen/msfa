<?php

namespace App\Http\Requests\Api\InterviewDetails;

use App\Http\Requests\Api\ApiBaseRequest;

class CheckPersonCdRequest extends ApiBaseRequest
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
            'visit_id' => 'required',
            'dataStock.*.person_cd' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'visit_id' => [
                'required' => __('validation.required'),
            ],
            'dataStock.*.person_cd' => [
                'required' => __('validation.required')
            ],
        ];
    }

    public function attributes()
    {
        return [
            'dataStock.*.person_cd' => '個人コード',
        ];
    }
}
