<?php

namespace App\Http\Requests\Api\FacilityGroup;

use App\Http\Requests\Api\ApiBaseRequest;

class ChangeSelectFacilityGroupRequest extends ApiBaseRequest
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
            'facility' => 'required|array',
            'facility.*.select_facility_group_id' => 'required|max:20',
            'facility.*.sort_order' => 'required|max:6',
        ];
    }

    public function messages()
    {
        return [
            'facility.required' =>  __('validation.required'),
            'facility.array' =>  __('validation.array'),
            'facility.*.select_facility_group_id.required' =>  config('messages.MSFA0179'),
            'facility.*.select_facility_group_id.max' =>   __('validation.max'),
            'facility.*.sort_order.required' =>  config('messages.MSFA0179'),
            'facility.*.sort_order.max' =>   __('validation.max')
        ];
    }

    public function attributes()
    {
        return [
            'facility.*.select_facility_group_id' => 'セレクト施設グループID',
            'facility.*.sort_order' => '表示順',
        ];
    }
}
