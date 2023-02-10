<?php

namespace App\Http\Requests\Api\KnowledgeManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class SearchPreRequest extends ApiBaseRequest {
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
            'user_cd' => 'nullable|array',
            'product_cd' => 'nullable|array',
            'knowledge_status_type' => 'nullable|array',
            'release_datetime_from' => 'nullable|date:Y-mm-dd',
            'release_datetime_to' => 'nullable|date:Y-mm-dd|after_or_equal:release_datetime_from'
        ];
    }

    public function messages() {
        return [
        ];
    }
}
