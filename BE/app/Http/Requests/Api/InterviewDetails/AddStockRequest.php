<?php

namespace App\Http\Requests\Api\InterviewDetails;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class AddStockRequest extends ApiBaseRequest
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
            'stock' => 'array|required',
            'stock.*.person_cd' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'schedule_id' => [
                'required' => __('validation.required'),
                'exists' => __('validation.exists')
            ],
            'visit_id' => [
                'required' => __('validation.required'),
                'exists' => __('validation.exists')
            ],
            'stock' => [
                'required' => __('validation.required'),
                'array' => __('validation.array')
            ],
            'stock.*.person_cd' => [
                'required' => __('validation.required'),
            ]
        ];
    }

    public function attributes()
    {
        return [
            'stock.*.person_cd' => '個人コード',
        ];
    }
}
