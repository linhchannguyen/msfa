<?php

namespace App\Http\Requests\Api\AccountSetting;

use App\Http\Requests\Api\ApiBaseRequest;

class EditAccountInfoRequest extends ApiBaseRequest {
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
            'note_1' => 'present|max:200',
            'note_2' => 'present|max:200',
            'note_3' => 'present|max:200',
            'note_4' => 'present|max:200',
            'note_5' => 'present|max:200'
        ];
    }

    public function messages() {
        return [
            'note_1' => [
                'max' => __('validation.max.string')
            ],
            'note_2' => [
                'max' => __('validation.max.string')
            ],
            'note_3' => [
                'max' => __('validation.max.string')
            ],
            'note_4' => [
                'max' => __('validation.max.string')
            ],
            'note_5' => [
                'max' => __('validation.max.string')
            ]
        ];
    }

    public function attributes() {
        return [
            'note_1' => '特記1',
            'note_2' => '特記2',
            'note_3' => '特記3',
            'note_4' => '特記4',
            'note_5' => '特記5'
        ];
    }
}
