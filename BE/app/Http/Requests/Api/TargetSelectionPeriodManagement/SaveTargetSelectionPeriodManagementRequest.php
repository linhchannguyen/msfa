<?php

namespace App\Http\Requests\Api\TargetSelectionPeriodManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveTargetSelectionPeriodManagementRequest extends ApiBaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'data' => 'nullable|array',
            'data.*.target_product_cd' => 'required',
            'data.*.selection_start_date' => 'required|date',
            'data.*.selection_end_date' => 'required|date',
            'data.*.start_date' => 'required|date',
            'data.*.end_date' => 'required|date',
        ];
    }

    public function messages() {
        return [
            'data' => [
                'array' => __('validation.array'),
            ],
            'data.*.target_product_cd' => [
                'required' => __('validation.required'),
            ],
            'data.*.selection_start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'data.*.selection_end_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'data.*.start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ],
            'data.*.end_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date'),
            ]
        ];
    }

    public function attributes(){
        return [
            'data' => 'データ',
            'data.*.target_product_cd' => 'ターゲット品目コード',
            'data.*.selection_start_date' => '選定期間開始日',
            'data.*.selection_end_date' => '選定期間終了日',
            'data.*.start_date' => '適用期間開始日',
            'data.*.end_date' => '適用期間終了日',
        ];
    }
}
