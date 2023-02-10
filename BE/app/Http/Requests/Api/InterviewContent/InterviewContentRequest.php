<?php

namespace App\Http\Requests\Api\InterviewContent;

use App\Http\Requests\Api\ApiBaseRequest;

class InterviewContentRequest extends ApiBaseRequest {
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
            'product_cd' => 'nullable|string',
            'content_cd' => 'nullable|string|max:10',
        ];
    }

    public function messages() {
        return [
            'product_cd' => [
                'max' => __('validation.max'),
            ],
            'content_cd' => [
                'max' => __('validation.max'),
            ]
        ];
    }
}
