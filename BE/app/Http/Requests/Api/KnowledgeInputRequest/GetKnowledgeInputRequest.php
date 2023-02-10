<?php

namespace App\Http\Requests\Api\KnowledgeInputRequest;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class GetKnowledgeInputRequest extends ApiBaseRequest
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
        if(!empty($this->knowledge_id)){
            $param = [
                [
                    "table" => "t_knowledge",
                    "condition" => [
                        "knowledge_id" => $this->knowledge_id
                    ]
                ]
            ];
            $this->validateInputParamater($param);
        }
        $rules['approval_work_type'] = ['required'];
        $rules['approval_layer_num'] = ['required'];

        return $rules;
    }

    public function messages()
    {
        return [
            'approval_work_type' => [
                'required' => __('validation.required'),
            ],
            'approval_layer_num' => [
                'required' => __('validation.required'),
            ],
        ];
    }

    public function attributes(){
        return [
            'approval_work_type' => '承認業務区分',
            'approval_layer_num' => '承認階層',
        ];
    }
}
