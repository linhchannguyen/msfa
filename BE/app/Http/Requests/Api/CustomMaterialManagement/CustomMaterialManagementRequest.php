<?php

namespace App\Http\Requests\Api\CustomMaterialManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class CustomMaterialManagementRequest extends ApiBaseRequest {
    protected $dateTimeFields = [
        "applicable_date" => 'Y-m-d',
    ];
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
            'applicable_date' => 'nullable|date:Y-mm-dd',
            'product_cd' => 'nullable|array'
        ];
    }

    public function messages() {
        return [];
    }
}
