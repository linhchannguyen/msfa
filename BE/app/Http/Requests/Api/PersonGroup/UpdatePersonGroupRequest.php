<?php

namespace App\Http\Requests\Api\PersonGroup;

use App\Http\Requests\Api\ApiBaseRequest;

class UpdatePersonGroupRequest extends ApiBaseRequest
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
            'select_person_group_id' => 'nullable|max:20',
            'select_person_group_name' => 'required|max:50',
            'sort_order' => 'required|max:6',
            'children' => 'nullable|array',
            'children.*.facility_category_type' => 'required|max:3',
            'children.*.facility_category_name' => 'required|max:100',
            'children.*.content_cd' => 'max:3',
            'children.*.children' => 'nullable|array',
            'children.*.children.*.facility_cd' => 'required|max:25',
            'children.*.children.*.facility_name' => 'required|max:255',
            'children.*.children.*.person_cd' => 'required|max:15',
            'children.*.children.*.person_name' => 'required|max:50',
            // 'children.*.children.*.department_cd' => 'required|max:4',
            // 'children.*.children.*.department_name' => 'required|max:100',
            // 'children.*.children.*.position_cd' => 'required|max:3',
            // 'children.*.children.*.position_name' => 'required|max:25',
        ];
    }

    public function messages()
    {
        return [
            'select_person_group_id.max' =>  __('validation.max'),
            'select_person_group_name.required' =>  __('validation.required'),
            'select_person_group_name.max' =>  __('validation.max'),
            'sort_order.required' =>  config('messages.MSFA0179'),
            'sort_order.max' =>  __('validation.max'),
            'children.array' =>  __('validation.array'),
            'children.*.facility_category_type.required' =>  config('messages.MSFA0179'),
            'children.*.facility_category_type.max' =>  __('validation.max'),
            'children.*.facility_category_name.required' => __('validation.required'),
            'children.*.facility_category_name.max' =>  __('validation.max'),
            'children.*.content_cd.max' =>  __('validation.max'),
            'children.*.children.array' =>  __('validation.array'),
            'children.*.children.*.facility_cd.required' => config('messages.MSFA0179'),
            'children.*.children.*.facility_cd.max' =>  __('validation.max'),
            'children.*.children.*.facility_name.required' => __('validation.required'),
            'children.*.children.*.facility_name.max' =>  __('validation.max'),
            'children.*.children.*.person_cd.required' => config('messages.MSFA0179'),
            'children.*.children.*.person_cd.max' =>  __('validation.max'),
            'children.*.children.*.person_name.required' => __('validation.required'),
            'children.*.children.*.person_name.max' =>  __('validation.max'),
            'children.*.children.*.department_cd.required' => __('validation.required'),
            'children.*.children.*.department_cd.max' =>  __('validation.max'),
            'children.*.children.*.department_name.required' => __('validation.required'),
            'children.*.children.*.department_name.max' =>  __('validation.max'),
            'children.*.children.*.position_cd.required' => __('validation.required'),
            'children.*.children.*.position_cd.max' =>  __('validation.max'),
            'children.*.children.*.position_name.required' => __('validation.required'),
            'children.*.children.*.position_name.max' =>  __('validation.max')
        ];
    }
    public function attributes()
    {
        return [
            'children.*.facility_category_type' =>  '????????????',
            'children.*.facility_category_name' => '???????????????',
            'children.*.content_cd' => '?????????????????????',
            'children.*.content_name' => '????????????',
            'children.*.product_cd' =>  '???????????????',
            'children.*.document_id' =>  '??????ID',
            'children.*.children' =>  __('validation.array'),
            'children.*.children.*.facility_cd' => '???????????????',
            'children.*.children.*.facility_name' => '?????????',
            'children.*.children.*.person_cd' => '???????????????',
            'children.*.children.*.person_name' => '?????????',
            'children.*.children.*.department_cd' => '?????????????????????',
            'children.*.children.*.department_name' => '???????????????',
            'children.*.children.*.position_cd' => '?????????????????????',
            'children.*.children.*.position_name' => '????????????'
        ];
    }
}
