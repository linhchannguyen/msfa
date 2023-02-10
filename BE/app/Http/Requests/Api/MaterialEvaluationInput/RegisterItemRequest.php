<?php

namespace App\Http\Requests\Api\MaterialEvaluationInput;

use App\Http\Requests\Api\ApiBaseRequest;

class RegisterItemRequest extends ApiBaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'data' => 'nullable|array',
            'data.*.document_id' => 'required',
            'data.*.usage_situation_id' => 'required',
            'data.*.document_review' => 'required',
            'data.*.document_name' => 'required',
        ];
    }

    public function messages() {
        return [
            'data' => [
                'array' => __('validation.array'),
            ],
            'data.*.document_id' => [
                'required' => __('validation.required'),
            ],
            'data.*.usage_situation_id' => [
                'required' => __('validation.required'),
            ],
            'data.*.document_review' => [
                'required' => __('validation.required'),
            ],
            'data.*.document_name' => [
                'required' => __('validation.required'),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'data.*.document_id' => '資材ID',
            'data.*.usage_situation_id' => '使用状況ID',
            'data.*.document_review' => '資材評価',
            'data.*.document_name' => '資材名',
        ];
    }
}
