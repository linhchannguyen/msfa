<?php

namespace App\Http\Requests\Api\KnowledgeInputRequest;

use App\Http\Requests\Api\ApiBaseRequest;

class ApproveRequest extends ApiBaseRequest
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
        $rules['active_layer_num'] = ['required'];
        $rules['request_id'] = ['required'];
        $rules['comment_orther_approval'] = ['nullable','array'];
        $rules['comment_orther_approval.layer_num'] = ['required'];
        $rules['comment_orther_approval.approval_user_cd'] = ['required'];
        $rules['comment_orther_approval.comment'] = ['nullable' , 'max:200'];
 
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
            'active_layer_num' => [
                'required' => __('validation.required'),
            ],
            'request_id' => [
                'required' => __('validation.required'),
            ],
            'comment_orther_approval' => [
                'array' => __('validation.array'),
            ],
            'comment_orther_approval.layer_num' => [
                'required' => __('validation.required'),
            ],
            'comment_orther_approval.approval_user_cd' => [
                'required' => __('validation.required'),
            ]
        ];
    }

    public function attributes(){
        return [
            'create_user_cd' => 'ユーザコード（作成者）',
            'approval_work_type' => '承認業務区分',
            'approval_layer_num' => '承認階層',
            'active_layer_num' => 'アクティブ承認階層',
            'comment_orther_approval' => '承認者コメント',
            'comment_orther_approval.layer_num' => '承認階層',
            'comment_orther_approval.approval_user_cd' => 'ユーザコード（承認者）',
            'comment_orther_approval.comment' => 'コメント',
        ];
    }
}
