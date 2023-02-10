<?php

namespace App\Http\Requests\Api\KnowledgeManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class InsertKnowledgeRequestRequest extends ApiBaseRequest {
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
            'knowledge_id' => 'required|max:20|exists:t_knowledge,knowledge_id',
            'demand_note' => 'required'
        ];
    }

    public function messages() {
        return [
        ];
    }
}