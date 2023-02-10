<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\ApiBaseRequest;
use App\Services\ContentFileConfig;

class ChangePasswordRequest extends ApiBaseRequest {
    private $contentFileConfigService;

    public function __construct(ContentFileConfig $contentFileConfigService)
    {
        $this->contentFileConfigService = $contentFileConfigService;
    }
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
            'current_password' => 'required',
            'new_password' => 'required|regex:/^'.$this->contentFileConfigService->getValueConfig(config('config_general_source.name_field.regex')).'$/'
        ];
        // |regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[a-zA-Z])[\x20-\x7E]{8,25}$/
    }

    public function messages() {
        $messageValidate = $this->contentFileConfigService->getValueConfig(config('config_general_source.name_field.regex_message'));
        return [
            'current_password' => [
                'required' => __('validation.required')
            ],
            'new_password' => [
                'required' => __('validation.required'),
                'regex' => $messageValidate ? $messageValidate :  __('validation.regex'),
            ],
        ];
    }
}
