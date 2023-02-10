<?php

namespace App\Http\Requests\Api\PersonSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class DepartmentPersonDetailRequest extends ApiBaseRequest
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
            'facility_cd' => 'required|max:25',
        ];
    }

    public function messages()
    {
        return [
            'person_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ]
        ];
    }
}
