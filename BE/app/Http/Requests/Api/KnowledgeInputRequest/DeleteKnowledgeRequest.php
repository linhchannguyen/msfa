<?php

namespace App\Http\Requests\Api\KnowledgeInputRequest;

use App\Http\Requests\Api\ApiBaseRequest;

class DeleteKnowledgeRequest extends ApiBaseRequest
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
            ]
        ];
    }

    public function attributes(){
        return [
            'create_user_cd' => 'ユーザコード（作成者）',
        ];
    }
}
