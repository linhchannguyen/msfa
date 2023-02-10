<?php

namespace App\Http\Requests\Api\QAManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class SearchQARequest extends ApiBaseRequest {
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
            'question_status_type' => 'nullable|array',
            'question_hot' => 'nullable|boolean',
            'new_arrival' => 'nullable|boolean',
            'unsuitable_report' => 'nullable|boolean'
        ];
    }

    public function messages() {
        return [];
    }
}
