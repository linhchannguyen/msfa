<?php

namespace App\Http\Requests\Api\KnowledgeInputRequest;

use App\Http\Requests\Api\ApiBaseRequest;

class RejectionRequest extends ApiBaseRequest
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
        $rules['knowledge_id'] = ['required'];
        $rules['create_user_cd'] = ['required'];
        $rules['approval_work_type'] = ['required'];
        $rules['approval_layer_num'] = ['required'];
        $rules['mode_screen'] = ['required'];

        if (count($this->comment_reject ?? []) > 0) {
            $rules['comment_reject.layer_num'] = ['required'];
            $rules['comment_reject.approval_user_cd'] = ['required'];
            $rules['comment_reject.comment'] = ['nullable' , 'max:200'];
        }
        

        if ($this->mode_screen == "admin") {
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
            $rules['time_line'] = ['nullable', 'array'];
            $rules['time_line.*.data'] = ['array'];
            $rules['time_line.*.data.*.timeline_id'] = ['required'];
            $rules['time_line.*.data.*.channel_type'] = ['required'];
            $rules['time_line.*.data.*.channel_id'] = ['required'];
            $rules['time_line.*.data.*.delete_flag'] = ['required'];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'knowledge_id' => [
                'required' => __('validation.required'),
            ],
            'submit_comment' => [
                'required' => __('validation.required'),
            ],
            'create_user_cd' => [
                'required' => __('validation.required'),
            ],
            'approval_work_type' => [
                'required' => __('validation.required'),
            ],
            'approval_layer_num' => [
                'required' => __('validation.required'),
            ],
            'mode_screen' => [
                'required' => __('validation.required'),
            ],
            'anonymous_flag' => [
                'required' => __('validation.required'),
            ],
            'title' => [
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
        ];
    }

    public function attributes()
    {
        return [
            'create_user_cd' => '?????????????????????????????????',
            'submit_comment' => '?????????????????????',
            'approval_work_type' => '??????????????????',
            'approval_layer_num' => '????????????',

            'comment_reject' => '?????????????????????',
            'comment_reject.layer_num' => '????????????',
            'comment_reject.approval_user_cd' => '?????????????????????????????????',
            'comment_reject.comment' => '????????????',

            'mode_screen' => '???????????????',
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
