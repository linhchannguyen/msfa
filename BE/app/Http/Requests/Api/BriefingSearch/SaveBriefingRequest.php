<?php

namespace App\Http\Requests\Api\BriefingSearch;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveBriefingRequest extends ApiBaseRequest
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
            'briefing_id' => 'nullable|max:20',
            'schedule_id' => 'nullable|max:20',
            'status_type' => 'required|max:2',
            'briefing_type' => 'nullable|max:2',
            'briefing_name' => 'required|max:100',
            'implement_user_cd' => 'required|max:15',
            'target_org_cd' => 'required|max:20',
            'facility_cd' => 'nullable|25',
            'medical_subjects_group_cd' => 'nullable|max:3',
            'place' => 'nullable|max:255',
            'disposal_technique' => 'nullable|max:2',
            'implement_user_name' => 'required|max:50',
            'implement_user_org_cd' => 'required|max:20',
            'implement_user_post' => 'nullable|max:2',
            'implement_user_post_name' => 'nullable|max:100',
            'implement_user_org_label' => 'required|max:30',
            'target_org_label' => 'required|max:30',
            'facility_short_name' => 'nullable|max:50',
            'result_only_flag' => 'required|max:1',
            'remand_flag' => 'required|1',
            'start_date' => 'required|date',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'briefing_attendee_count' => 'nullable|array',
            'briefing_attendee_count.*.occupation_type' => 'required|max:10',
            'briefing_attendee_count.*.plan_headcount' => 'nullable|max:4',
            'briefing_attendee_count.*.result_headcount' => 'nullable|max:4',
            'briefing_expense_item' => 'nullable|array',
            'briefing_expense_item.*.expense_item_cd' => 'required|max:3',
            'briefing_expense_item.*.plan_unit_price' => 'nullable|max:8',
            'briefing_expense_item.*.plan_quantity' => 'nullable|max:4',
            'briefing_expense_item.*.plan_amount' => 'nullable|max:8',
            'briefing_expense_item.*.result_unit_price' => 'nullable|max:8',
            'briefing_expense_item.*.result_quantity' => 'nullable|max:4',
            'briefing_expense_item.*.result_amount' => 'nullable|max:8',
            'briefing_product' => 'nullable|array',
            'briefing_product.*.product_cd' => 'required|max:10',
            'briefing_product.*.product_name' => 'nullable|max:30',
            'briefing_document' => 'nullable|array',
            'briefing_document.*.document_id' => 'required|max:20',
            'briefing_attendee' => 'nullable|array',
            'briefing_attendee.*.briefing_attendee_id' => 'required|max:20',
            'briefing_attendee.*.briefing_id' => 'required|max:20',
            'briefing_attendee.*.facility_cd' => 'required|max:25',
            'briefing_attendee.*.facility_short_name' => 'nullable|max:50',
            'briefing_attendee.*.department_cd' => 'nullable|max:4',
            'briefing_attendee.*.department_name' => 'nullable|max:100',
            'briefing_attendee.*.person_cd' => 'required|max:15',
            'briefing_attendee.*.person_name' => 'required|max:50',
            'briefing_attendee.*.display_position_cd' => 'nullable|max:3',
            'briefing_attendee.*.display_position_name' => 'nullable|max:25',
            'briefing_attendee.*.medical_staff_cd' => 'nullable|max:15',
            'briefing_attendee.*.other_person_flag' => 'required|max:1',
        ];
    }

    public function messages()
    {
        return [
            'delete_flags' => [
                'required' => __('validation.required'),
                'array' => __('validation.array')
            ],
            'display_position_cd' => [
                'max' => __('validation.max')
            ],
            'display_position_name' => [
                'max' => __('validation.max')
            ],
            'medical_staff_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'other_person_flag' => [
                'required' => __('validation.required'),
                'array' => __('validation.array')
            ],
            'department_cd' => [
                'max' => __('validation.max')
            ],
            'department_name' => [
                'max' => __('validation.max')
            ],
            'person_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'person_name' => [
                'array' => __('validation.array')
            ],
            'briefing_attendee_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'briefing_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'facility_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'facility_short_name' => [
                'array' => __('validation.array')
            ],
            'briefing_attendee' => [
                'array' => __('validation.array')
            ],
            'document_id' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'briefing_document' => [
                'array' => __('validation.array')
            ],
            'product_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'product_name' => [
                'max' => __('validation.max')
            ],
            'briefing_product' => [
                'array' => __('validation.array')
            ],
            'plan_amount' => [
                'max' => __('validation.max')
            ],
            'result_unit_price' => [
                'max' => __('validation.max')
            ],
            'result_quantity' => [
                'max' => __('validation.max')
            ],
            'result_amount' => [
                'max' => __('validation.max')
            ],
            'expense_item_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'plan_unit_price' => [
                'max' => __('validation.max')
            ],
            'plan_quantity' => [
                'max' => __('validation.max')
            ],
            'briefing_expense_item' => [
                'array' => __('validation.array')
            ],
            'occupation_type' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'plan_headcount' => [
                'max' => __('validation.max')
            ],
            'result_headcount' => [
                'max' => __('validation.max')
            ],
            'briefing_attendee_count' => [
                'array' => __('validation.array')
            ],
            'end_time' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'start_time' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'start_date' => [
                'required' => __('validation.required'),
                'date' => __('validation.date')
            ],
            'remand_flag' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'result_only_flag' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'facility_short_name' => [
                'max' => __('validation.max')
            ],
            'target_org_label' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'implement_user_org_label' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'implement_user_post_name' => [
                'max' => __('validation.max')
            ],
            'disposal_technique' => [
                'max' => __('validation.max')
            ],
            'implement_user_name' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'implement_user_org_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'implement_user_post' => [
                'max' => __('validation.max')
            ],
            'target_org_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'facility_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'medical_subjects_group_cd' => [
                'max' => __('validation.max')
            ],
            'place' => [
                'max' => __('validation.max')
            ],
            'implement_user_cd' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'status_type' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'briefing_type' => [
                'max' => __('validation.max')
            ],
            'briefing_name' => [
                'required' => __('validation.required'),
                'max' => __('validation.max')
            ],
            'schedule_id' => [
                'max' => __('validation.max')
            ],
            'briefing_id' => [
                'max' => __('validation.max')
            ]
        ];
    }
}
