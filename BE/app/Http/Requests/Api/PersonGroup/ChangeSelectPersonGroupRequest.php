<?php

namespace App\Http\Requests\Api\PersonGroup;

use App\Http\Requests\Api\ApiBaseRequest;

class ChangeSelectPersonGroupRequest extends ApiBaseRequest
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
            'person' => 'required|array',
            'person.*.sort_order' => 'required|max:6',
            'person.*.select_person_group_id' => 'required|max:20',
        ];
    }

    public function messages()
    {
        return [
            'person.required' =>  __('validation.required'),
            'person.array' =>  __('validation.array'),
            'person.*.select_person_group_id.required' =>  config('messages.MSFA0179'),
            'person.*.select_person_group_id.max' =>   __('validation.max'),
            'person.*.sort_order.required' =>  config('messages.MSFA0179'),
            'person.*.sort_order.max' =>   __('validation.max')
        ];
    }
}
