<?php

namespace App\Http\Requests\Api\InterviewDetailedInput;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveRequest extends ApiBaseRequest
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
        $rules = [];
        $rules['call_id'] = ['required'];
        $rules['schedule_id'] = ['required'];
        $rules['facility_cd'] = ['required'];
        $rules['person_cd'] = ['required'];
        $rules['start_date'] = ['required' , 'date'];
        $rules['start_time'] = ['required' , 'date'];
        $rules['off_label_flag'] = ['required'];
        if ($this->off_label_flag == 1) {
            $rules['off_label_concent_flag'] = ['required'];
            $rules['off_label_call_time'] = ['required'];
            $rules['related_product'] = ['required','max:50'];
            $rules['question'] = ['required','max:300'];
            $rules['answer'] = ['required','max:300'];
            $rules['re_question'] = ['required','max:300'];
            $rules['literature'] = ['required','max:100'];
        }

        // detailArea
        $rules['detailArea'] = ['nullable', 'array'];
        $rules['detailArea.*.detail_order'] = ['required'];
        $rules['detailArea.*.content_cd'] = ['required'];
        $rules['detailArea.*.content_name_tran'] = ['required'];
        $rules['detailArea.*.product_cd'] = ['required','max:10'];
        $rules['detailArea.*.product_name'] = ['required','max:30'];
        $rules['detailArea.*.reaction_cd'] = ['nullable','max:4'];
        $rules['detailArea.*.reaction'] = ['nullable','max:20'];
        $rules['detailArea.*.phase_cd'] = ['nullable','max:4'];
        $rules['detailArea.*.phase'] = ['nullable','max:20'];
        $rules['detailArea.*.remarks'] = ['nullable','max:300'];
        $rules['detailArea.*.prescription_count'] = ['nullable','max:11'];

        // materials
        $rules['detailArea.*.materials'] = ['array' , 'nullable'];
        $rules['detailArea.*.materials.*.document_id'] = ['required'];

        // deleteArray
        $rules['detailArea.*.deleteArray'] = ['nullable', 'array'];
        $rules['detailArea.*.deleteArray.*.detail_id'] = ['required'];
        $rules['detailArea.*.deleteArray.*.product_cd'] = ['required'];

        return $rules;
    }

    public function messages()
    {
        return [
            'call_id' => [
                'required' => __('validation.required'),
            ],
            'schedule_id' => [
                'required' => __('validation.required'),
            ],
            'facility_cd' => [
                'required' => __('validation.required'),
            ],
            'person_cd' => [
                'required' => __('validation.required'),
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'start_time' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'off_label_flag' => [
                'required' => __('validation.required'),
            ],
            'off_label_concent_flag' => [
                'required' => __('validation.required'),
            ],
            'off_label_call_time' => [
                'required' => __('validation.required'),
            ],
            'related_product' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'question' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'answer' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            're_question' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'literature' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],

            'detailArea' => [
                'array' => __('validation.array'),
            ],

            'detailArea.*.detail_order' => [
                'required' => __('validation.required'),
            ],
            'detailArea.*.content_cd' => [
                'required' => __('validation.required'),
            ],
            'detailArea.*.content_name_tran' => [
                'required' => __('validation.required'),
            ],
            'detailArea.*.product_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'detailArea.*.product_name' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'detailArea.*.reaction_cd' => [
                'max' => __('validation.max'),
            ],
            'detailArea.*.reaction' => [
                'required' => __('validation.required'),
                'max' => __('validation.max'),
            ],
            'detailArea.*.phase_cd' => [
                'max' => __('validation.max'),
            ],
            'detailArea.*.phase' => [
                'max' => __('validation.max'),
            ],
            'detailArea.*.remarks' => [
                'max' => __('validation.max'),
            ],
            'detailArea.*.prescription_count' => [
                'max' => __('validation.max'),
            ],
            'detailArea.*.materials' => [
                'array' => __('validation.array'),
            ],

            'detailArea.*.materials.*.document_id' => [
                'required' => __('validation.required'),
            ],

            'detailArea.*.deleteArray' => [
                'array' => __('validation.array'),
            ],

            'detailArea.*.deleteArray.*.detail_id' => [
                'required' => __('validation.required'),
            ],

            'detailArea.*.deleteArray.*.product_cd' => [
                'required' => __('validation.required'),
            ]
        ];
    }

    public function attributes()
    {
        return [
            'off_label_flag' => 'オフラベル有フラグ',
            'off_label_concent_flag' => 'オフラベル承諾フラグ',
            'off_label_call_time' => 'オフラベル面談時間',
            'related_product' => '自社関連製品',
            'question' => '要求質問内容',
            'answer' => '回答内容',
            're_question' => '回答に対する質問',
            'literature' => '文献名',
            'detailArea' => 'ディテールエリア',
            'detailArea.*.detail_order' => 'ディテール順',
            'detailArea.*.content_cd' => '面談内容コード',
            'detailArea.*.content_name_tran' => '面談内容(当時)',
            'detailArea.*.product_cd' => '品目コード',
            'detailArea.*.product_name' => '品目名',
            'detailArea.*.reaction_cd' => '反応コード',
            'detailArea.*.reaction' => '反応(当時)',
            'detailArea.*.phase_cd' => 'フェーズコード',
            'detailArea.*.phase' => 'フェーズ(当時)',
            'detailArea.*.remarks' => '活動メモ',
            'detailArea.*.prescription_count' => '症例数',

            'detailArea.*.materials' => '資材' ,
            'detailArea.*.materials.*.document_id' => '資材ID' ,
            'detailArea.*.deleteArray' => '',
            'detailArea.*.deleteArray.*.detail_id' => 'ディテールID ' ,
            'detailArea.*.deleteArray.*.product_cd' => '品目コード ',

            'start_date' => '活動日付',
            'start_time' => '開始日時'
        ];
    }
}
