<?php

namespace App\Http\Requests\Api\FacilityGroup;

use App\Http\Requests\Api\ApiBaseRequest;

class UpdateFacilityGroupRequest extends ApiBaseRequest
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
            'select_facility_group_id' => 'nullable|max:20',
            'select_facility_group_name' => 'required|max:50',
            'sort_order' => 'required|numeric',
            'list_facility' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'select_facility_group_id.max' =>  __('validation.max'),
            'select_facility_group_name.required' =>  config('messages.MSFA0179'),
            'sort_order.required' =>  config('messages.MSFA0179'),
            'list_facility.array' =>  __('validation.array')
        ];
    }
}
