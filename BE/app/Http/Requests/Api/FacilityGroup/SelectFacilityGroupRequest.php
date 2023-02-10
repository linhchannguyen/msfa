<?php

namespace App\Http\Requests\Api\FacilityGroup;

use App\Http\Requests\Api\ApiBaseRequest;

class SelectFacilityGroupRequest extends ApiBaseRequest
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
            'user_cd' => 'nullable|string|max:15',
        ];
    }

    public function messages()
    {
        return [
            'user_cd' => [
                'max' => __('validation.max')
            ]
        ];
    }
}
