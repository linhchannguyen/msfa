<?php

namespace App\Http\Requests\Api\InFacilityTargetPersonManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class SaveInFacilityTargetPersonRequest extends ApiBaseRequest {
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
            'records.*.facility_cd' => 'required',
            'records.*.person_cd' => 'required',
            'records.*.segment_list' => 'nullable|array',
            'records.*.segment_list.*.segment_type' => 'required',
            'records.*.target_product_list' => 'nullable|array',
            'records.*.target_product_list.*.target_product_cd' => 'required',
        ];
    }

    public function messages() {
        return [
            'records' => [
                'array' => __('validation.array'),
            ],
            'records.*.facility_cd' => [
                'required' => __('validation.required'),
            ],
            'records.*.person_cd' => [
                'required' => __('validation.required'),
            ],
            'records.*.segment_list' => [
                'array' => __('validation.array'),
            ],
            'records.*.segment_list.*.segment_type' => [
                'required' => __('validation.required'),
            ],
            'records.*.target_product_list' => [
                'array' => __('validation.array'),
            ],
            'records.*.target_product_list.*.target_product_cd' => [
                'required' => __('validation.required'),
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
