<?php

namespace App\Http\Requests\Api\FacilityGroup;

use App\Http\Requests\Api\ApiBaseRequest;

class DeleteFacilityGroupRequest extends ApiBaseRequest
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
            'select_facility_group_id' => 'required|max:20',
        ];
    }

    public function messages()
    {
        return [
            'select_facility_group_id.required' =>  config('messages.MSFA0179'),
        ];
    }
}
