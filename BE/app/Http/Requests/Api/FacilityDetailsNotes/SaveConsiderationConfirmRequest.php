<?php

namespace App\Http\Requests\Api\FacilityDetailsNotes;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveConsiderationConfirmRequest extends ApiBaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'facility_consideration_id' => 'required',
            'confirmed_flag' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'facility_consideration_id' => [
                'required' => __('validation.required')
            ],
            'confirmed_flag' => [
                'required' => __('validation.required')
            ],
        ];
    }
}
