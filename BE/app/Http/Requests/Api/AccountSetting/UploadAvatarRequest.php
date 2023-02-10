<?php

namespace App\Http\Requests\Api\AccountSetting;

use App\Http\Requests\Api\ApiBaseRequest;

class UploadAvatarRequest extends ApiBaseRequest {
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
            'avatar' => 'required|file|image|mimes:jpeg,jpg,jpe,png|max:2000'
        ];
    }

    public function messages() {
        return [
            'avatar' => [
                'required' => __('validation.file'),
                'file' => __('validation.file'),
                'image' => __('validation.image'),
                'max' => __('validation.max.file'),
                'mimes' => __('validation.mimes'),
            ]
        ];
    }

    public function attributes() {
        return [
            'avatar' => 'プロフィール画像フ'
        ];
    }
}
