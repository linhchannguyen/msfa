<?php

namespace App\Http\Requests\Api\LectureSeriesSelection;

use App\Http\Requests\Api\ApiBaseRequest;

class RegisterRequest extends ApiBaseRequest {
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
            'convention_id' => 'required',
            'parent_series_flag' => 'required',
        ];
    }

    public function messages() {
        return [
            'convention_id' => [
                'required' => __('validation.required'),
            ],
            'parent_series_flag' => [
                'required' => __('validation.required'),
            ],
        ];
    }

    public function attributes()
    {
        return [
            'convention_id' => '講演会ID',
            'parent_series_flag' => '親シリーズフラグ',
        ];
    }
}
