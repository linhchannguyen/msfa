<?php

namespace App\Http\Requests\Api\CustomMaterialManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class CopyCustomMaterialRequest extends ApiBaseRequest {
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
            'document_id' => 'required'
        ];
    }

    public function messages() {
        return [];
    }
}
