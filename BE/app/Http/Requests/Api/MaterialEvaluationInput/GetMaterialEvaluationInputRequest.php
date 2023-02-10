<?php

namespace App\Http\Requests\Api\MaterialEvaluationInput;

use App\Http\Requests\Api\ApiBaseRequest;

class GetMaterialEvaluationInputRequest extends ApiBaseRequest {
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
            'document_usage_type' => 'required',
            'document_usage_id' => 'required'
        ];
    }

    public function messages() {
        return [
            'document_usage_type' => [
                'required' => __('validation.required'),
            ],
            'document_usage_id' => [
                'required' => __('validation.required'),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'document_usage_type' => '資材使用機能区分',
            'document_usage_id' => '資材使用機能ID',
        ];
    }
}
