<?php

namespace App\Http\Requests\Api\QAManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class UpsertRequest extends ApiBaseRequest {
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
            'question_category_cd' => 'required|max:3',
            'title' => 'required|max:30',
            'contents' => 'required'
        ];
    }

    public function messages() {
        return [
        ];
    }
}
