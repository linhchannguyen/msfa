<?php

namespace App\Http\Requests\Api\ConventionSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveConventionDetailRequest extends ApiBaseRequest {
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
            'convention_id' => 'nullable|max:20',
            'schedule_id' => 'nullable|max:20',
            'start_date' => 'required|date',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'convention_name' => 'required|max:100',
            'convention_type' => 'nullable|max:2',
            'cohost_partner_name' => 'nullable|max:40',
            'disposal_technique' => 'nullable|max:2',
            'hold_form' => 'nullable|max:2',
            'hold_method' => 'nullable|max:2',
            'hold_type' => 'nullable|max:2',
            'parent_series_flag' => 'nullable|max:1',
            'place' => 'nullable|max:255',
            'series_convention_id' => 'nullable|max:20',
            'status_type' => 'nullable|max:2',
            'convention_product' => 'nullable|array',
            'convention_product.*.product_cd' => 'required|max:10',
            'convention_target_org' => 'nullable|array',
            'convention_target_org.*.org_cd' => 'required|max:20',
            'convention_document' => 'nullable|array',
            'convention_document.*.document_id' => 'required|max:20',
            'convention_file' => 'nullable|array',
            'convention_file.*.file_id' => 'nullable|max:20',
            'convention_file.*.file' => 'nullable|max:102400',
            // 'area_expense' => 'required|array',
            // 'area_expense.*.expense_item_cd' => 'required|max:3',
            // 'area_expense.*.plan_amount' => 'required|max:8',
            // 'area_expense.*.result_amount' => 'required|max:8',
            // 'area_expense.*.result_amount' => 'required|max:8',
            // 'area_expense.*.layer_2' => 'required|array',
            // 'area_expense.*.layer_2.*.expense_item_cd' => 'required|max:3',
            // 'area_expense.*.layer_2.*.layer_num' => 'required|max:1',
            // 'area_expense.*.layer_2.*.option_item_content' => 'nullable|max:20',
            // 'area_expense.*.layer_2.*.option_item_name' => 'nullable|max:50',
            // 'area_expense.*.layer_2.*.plan_amount' => 'nullable|max:8',
            // 'area_expense.*.layer_2.*.plan_quantity' => 'nullable|max:4',
            // 'area_expense.*.layer_2.*.plan_unit_price' => 'nullable|max:8',
            // 'area_expense.*.layer_2.*.result_amount' => 'nullable|max:8',
            // 'area_expense.*.layer_2.*.result_quantity' => 'nullable|max:4',
            // 'area_expense.*.layer_2.*.result_unit_price' => 'nullable|max:8',
            // 'area_expense.*.layer_2.*.layer_3' => 'nullable|array',
            // 'area_expense.*.layer_2.*.layer_3.*.expense_item_cd' => 'required|max:3',
            // 'area_expense.*.layer_2.*.layer_3.*.layer_num' => 'required|max:1',
            // 'area_expense.*.layer_2.*.layer_3.*.option_item_content' => 'nullable|max:20',
            // 'area_expense.*.layer_2.*.layer_3.*.option_item_name' => 'nullable|max:50',
            // 'area_expense.*.layer_2.*.layer_3.*.plan_amount' => 'nullable|max:8',
            // 'area_expense.*.layer_2.*.layer_3.*.plan_quantity' => 'nullable|max:4',
            // 'area_expense.*.layer_2.*.layer_3.*.plan_unit_price' => 'nullable|max:8',
            // 'area_expense.*.layer_2.*.layer_3.*.result_amount' => 'nullable|max:8',
            // 'area_expense.*.layer_2.*.layer_3.*.result_quantity' => 'nullable|max:4',
            // 'area_expense.*.layer_2.*.layer_3.*.result_unit_price' => 'nullable|max:8',
        ];
    }

    public function messages() {
        return [
            'area_expense.*.layer_2.*.layer_3.*.expense_item_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3.*.layer_num' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3.*.option_item_content' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3.*.option_item_name' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3.*.plan_amount' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3.*.plan_quantity' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3.*.plan_unit_price' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3.*.result_amount' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3.*.result_quantity' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3.*.result_unit_price' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.expense_item_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_num' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.option_item_content' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.option_item_name' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.plan_amount' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.plan_quantity' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.plan_unit_price' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.result_amount' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.result_quantity' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.result_unit_price' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.layer_2.*.layer_3' => [
                'array' => __('validation.array')
            ],
            'area_expense.*.layer_2' => [
                'required' => __('validation.required'),
                'array' => __('validation.array')
            ],
            'area_expense.*.expense_item_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'area_expense.*.plan_amount' => [
                'max' => __('validation.max')
            ],
            'area_expense.*.result_amount' => [
                'max' => __('validation.max')
            ],
            'area_expense' => [
                'required' => __('validation.required'),
                'array' => __('validation.array')
            ],
            'convention_file.*.file' => [
                'max' => __('validation.max')
            ],
            'convention_file.*.file_id' => [
                'max' => __('validation.max')
            ],
            'convention_file' => [
                'array' => __('validation.array')
            ],
            'convention_document.*.document_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'convention_document' => [
                'array' => __('validation.array')
            ],
            'convention_target_org.*.org_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'convention_target_org' => [
                'array' => __('validation.array')
            ],
            'convention_product.*.product_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'convention_product' => [
                'array' => __('validation.array')
            ],
            'place' => [
                'max' => __('validation.max')
            ],
            'series_convention_id' => [
                'max' => __('validation.max')
            ],
            'status_type' => [
                'max' => __('validation.max')
            ],
            'parent_series_flag' => [
                'max' => __('validation.max')
            ],
            'hold_form' => [
                'max' => __('validation.max')
            ],
            'hold_method' => [
                'max' => __('validation.max')
            ],
            'hold_type' => [
                'max' => __('validation.max')
            ],
            'disposal_technique' => [
                'max' => __('validation.max')
            ],
            'cohost_partner_name' => [
                'max' => __('validation.max')
            ],
            'convention_type' => [
                'max' => __('validation.max')
            ],
            'request_type' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'convention_name' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'start_time' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'start_time' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ]
        ];
    }
}
