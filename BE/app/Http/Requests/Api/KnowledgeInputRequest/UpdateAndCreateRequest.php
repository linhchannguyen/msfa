<?php

namespace App\Http\Requests\Api\KnowledgeInputRequest;

use App\Http\Requests\Api\ApiBaseRequest;

class UpdateAndCreateRequest extends ApiBaseRequest
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
        if($this->mode_screen == "edit"){
            $rules['knowledge_id'] = ['required'];
        }
        $rules['anonymous_flag'] = ['required'];
        $rules['title'] = ['required'];
        $rules['knowledge_category_cd'] = ['required'];
        $rules['product_cd'] = ['required'];
        $rules['product_name'] = ['required'];
        $rules['facility_cd'] = ['required'];
        $rules['facility_short_name'] = ['required'];
        $rules['medical_subjects_group_cd'] = ['required'];
        $rules['medical_subjects_group_name'] = ['required'];
        $rules['users'] = ['array'];
        $rules['users.*.user_cd'] = ['required'];
        $rules['time_line'] = ['nullable' , 'array'];
        $rules['time_line.*.data'] = ['array'];
        $rules['time_line.*.data.*.timeline_id'] = ['required'];
        $rules['time_line.*.data.*.channel_type'] = ['required'];
        $rules['time_line.*.data.*.channel_id'] = ['required'];
        $rules['time_line.*.data.*.delete_flag'] = ['required'];
        $rules['mode_screen'] = ['required'];
        $rules['approval_work_type'] = ['required'];
        $rules['approval_layer_num'] = ['required'];

        return $rules;
    }

    public function messages()
    {
        return [
            'knowledge_id' => [
                'required_if' => __('validation.required_if'),
            ],
            'approval_work_type' => [
                'required' => __('validation.required_if'),
            ],
            'approval_layer_num' => [
                'required' => __('validation.required_if'),
            ],
            'anonymous_flag' => [
                'required' => __('validation.required'),
            ],
            'title' => [
                'required' => __('validation.required'),
            ],
            'knowledge_category_cd' => [
                'required' => __('validation.required'),
            ],
            'product_cd' => [
                'required' => __('validation.required'),
            ],
            'product_name' => [
                'required' => __('validation.required'),
            ],
            'facility_cd' => [
                'required' => __('validation.required'),
            ],
            'facility_short_name' => [
                'required' => __('validation.required'),
            ],
            'medical_subjects_group_cd' => [
                'required' => __('validation.required'),
            ],
            'medical_subjects_group_name' => [
                'required' => __('validation.required'),
            ],
            'users' => [
                'array' => __('validation.array'),
            ],
            'users.*.user_cd' => [
                'required' => __('validation.required'),
            ],
            'time_line' => [
                'array' => __('validation.array'),
            ],
            'time_line.*.data' => [
                'required' => __('validation.required'),
                'array' => __('validation.array'),
            ],
            'time_line.*.data.*.timeline_id' => [
                'required' => __('validation.required'),
            ],
            'time_line.*.data.*.channel_type' => [
                'required' => __('validation.required'),
            ],
            'time_line.*.data.*.channel_id' => [
                'required' => __('validation.required'),
            ],
            'time_line.*.data.*.delete_flag' => [
                'required' => __('validation.required'),
            ],
            'mode_screen' => [
                'required' => __('validation.required'),
            ],
        ];
    }

    public function attributes(){
        return [
            'mode_screen' => '???????????????',
            'edit' => '???????????????',
            'approval_work_type' => '??????????????????',
            'approval_layer_num' => '????????????',
            'facility_cd' => '???????????????????????????',
            'facility_short_name' => '????????????????????????',
            'medical_subjects_group_cd' => '???????????????????????????????????????',
            'medical_subjects_group_name' => '?????????????????????????????????',
            'users' => '????????????',
            'users.*.user_cd' => '?????????????????? ',
            'time_line' => '??????????????????',
            'time_line.*.data' => '?????????',
            'time_line.*.data.*.timeline_id' => '??????????????????ID',
            'time_line.*.data.*.delete_flag' => '???????????????',
            'time_line.*.data.*.channel_type' => '????????????',
            'time_line.*.data.*.channel_id' => '??????ID ',
        ];
    }
}
