<?php

namespace App\Http\Requests\Api\PersonGroup;

use App\Http\Requests\Api\ApiBaseRequest;

class DeletePersonGroupRequest extends ApiBaseRequest
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
            'select_person_group_id' => 'required|max:20',
        ];
    }

    public function messages()
    {
        return [
            'select_person_group_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ]
        ];
    }
}
