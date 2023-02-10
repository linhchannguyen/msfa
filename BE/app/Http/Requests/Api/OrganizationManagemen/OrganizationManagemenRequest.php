<?php

namespace App\Http\Requests\Api\OrganizationManagemen;

use App\Http\Requests\Api\ApiBaseRequest;

class OrganizationManagemenRequest extends ApiBaseRequest {
    protected $dateTimeFields = [
        "date" => 'Y-m-d'
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
            'date' => 'required|date',
        ];
    }

    public function messages() {
        return [
            'date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ]
        ];
    }
}
