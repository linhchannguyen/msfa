<?php

namespace App\Http\Requests\Api\FacilityDetailsNotes;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveNewModeRequest extends ApiBaseRequest
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
            'facility_cd' => 'required',
            'consideration_type' => 'required',
            // 'confirmation_request_destination' => 'required',
            'consideration_contents' => 'required',
            'facility_short_name' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'facility_cd' => [
                'required' => __('validation.required')
            ],
            'consideration_type' => [
                'required' => __('validation.required')
            ],
            // 'confirmation_request_destination' => [
            //     'required' => __('validation.required')
            // ],
            'consideration_contents' => [
                'required' => __('validation.required')
            ],
            'facility_short_name' => [
                'required' => __('validation.required')
            ],
        ];
    }

    public function attributes(){
        return [
            'confirmation_request_destination' => '確認依頼先',
            'consideration_contents' => '注意事項',
        ];
    }
}
