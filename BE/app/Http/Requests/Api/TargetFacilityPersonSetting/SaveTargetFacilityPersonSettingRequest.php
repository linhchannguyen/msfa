<?php

namespace App\Http\Requests\Api\TargetFacilityPersonSetting;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveTargetFacilityPersonSettingRequest extends ApiBaseRequest {
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
            'records' => 'nullable|array',
            'records.*.facility_cd' => 'required|exists:m_facility,facility_cd',
            'records.*.person_cd' => 'required|exists:m_person,person_cd',
            'records.*.segment_list' => 'nullable|array',
            'records.*.segment_list.*.segment_type' => 'required|exists:m_facility_person_segment_type,segment_type',
            'records.*.target_product_list' => 'nullable|array',
            'records.*.target_product_list.*.target_product_cd' => 'required|exists:m_target_product,target_product_cd',
        ];
    }

    public function messages() {
        return [
            'records' => [
                'array' => __('validation.array'),
            ],
            'records.*.facility_cd' => [
                'required' => __('validation.required'),
                'exists' => __('validation.exists'),
            ],
            'records.*.person_cd' => [
                'required' => __('validation.required'),
                'exists' => __('validation.exists'),
            ],
            'records.*.segment_list' => [
                'array' => __('validation.array'),
            ],
            'records.*.segment_list.*.segment_type' => [
                'required' => __('validation.required'),
                'exists' => __('validation.exists'),
            ],
            'records.*.target_product_list' => [
                'array' => __('validation.array'),
            ],
            'records.*.target_product_list.*.target_product_cd' => [
                'required' => __('validation.required'),
                'exists' => __('validation.exists'),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'records' => 'レコードリスト',
            'records.*.facility_cd' => '施設コード',
            'records.*.person_cd' => '個人コード',
            'records.*.segment_list' => 'セグメントリスト',
            'records.*.segment_list.*.segment_type' => 'セグメント区分',
            'records.*.target_product_list' => '対象商品一覧',
            'records.*.target_product_list.*.target_product_cd' => 'ターゲット品目コード',
        ];
    }
}
