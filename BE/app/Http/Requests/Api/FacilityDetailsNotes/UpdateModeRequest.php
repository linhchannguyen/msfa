<?php

namespace App\Http\Requests\Api\FacilityDetailsNotes;

use App\Http\Requests\Api\ApiBaseRequest;

class UpdateModeRequest extends ApiBaseRequest
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
            'facility_consideration_id' => 'required',
            'consideration_type' => 'required',
            'confirmation_request_destination' => 'nullable|array',
            'confirmation_request_destination.*.user_cd' => 'required',
            'facility_short_name' => 'required',
            'consideration_contents' => 'required',
            'confirmation_request_delete' => 'nullable|array',
            'confirmation_request_delete.*.user_cd' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'facility_cd' => [
                'required' => __('validation.required')
            ],
            'facility_consideration_id' => [
                'required' => __('validation.required')
            ],
            'consideration_type' => [
                'required' => __('validation.required')
            ],
            'consideration_contents' => [
                'required' => __('validation.required')
            ],
            'confirmation_request_destination' => [
                'array' => __('validation.array')
            ],
            'confirmation_request_destination.*.user_cd' => [
                'required' => __('validation.required')
            ],

            'confirmation_request_delete' => [
                'array' => __('validation.array')
            ],
            'confirmation_request_delete.*.user_cd' => [
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
            'confirmation_request_destination.*.user_cd' => 'ユーザコード ',
            'confirmation_request_delete' => '確認依頼先delete',
            'confirmation_request_delete.*.user_cd' => 'ユーザコード',
        ];
    }
}
