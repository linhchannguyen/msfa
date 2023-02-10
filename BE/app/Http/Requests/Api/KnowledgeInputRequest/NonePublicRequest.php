<?php

namespace App\Http\Requests\Api\KnowledgeInputRequest;

use App\Http\Requests\Api\ApiBaseRequest;

class NonePublicRequest extends ApiBaseRequest
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
        $rules['approval_work_type'] = ['required'];
        $rules['approval_layer_num'] = ['required'];
        $rules['create_user_cd'] = ['required'];
        $rules['mode_screen'] = ['required'];

        if(count($this->comment_none_public ?? []) > 0){
            $rules['comment_none_public.layer_num'] = ['required'];
            $rules['comment_none_public.approval_user_cd'] = ['required'];
            $rules['comment_none_public.comment'] = ['nullable' , 'max:200'];
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

            'comment_none_public' => [
                'array' => __('validation.array'),
            ],
            'comment_none_public.layer_num' => [
                'required' => __('validation.required'),
            ],
            'comment_none_public.approval_user_cd' => [
                'required' => __('validation.required'),
            ]
        ];
    }

    public function attributes()
    {
        return [
            'create_user_cd' => 'ユーザコード（作成者）',
            'approval_work_type' => '承認業務区分',
            'approval_layer_num' => '承認階層',
            'mode_screen' => 'モード画面',

            'users' => 'ユーザー',
            'users.*.user_cd' => 'ユーザーコード ',
            'time_line' => 'タイムライン',
            'time_line.*.data' => 'データ',
            'time_line.*.data.*.timeline_id' => 'タイムラインID',
            'time_line.*.data.*.delete_flag' => '削除フラグ',
            'time_line.*.data.*.channel_type' => '活動区分',
            'time_line.*.data.*.channel_id' => '活動ID ',

            'comment_none_public' => 'コメントなし公開',
            'comment_none_public.layer_num' => '承認階層',
            'comment_none_public.approval_user_cd' => 'ユーザコード（承認者）',
            'comment_none_public.comment' => 'コメント',
        ];
    }
}
