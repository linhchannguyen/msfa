<?php

namespace App\Http\Requests\Api\Message;

use App\Http\Requests\Api\ApiBaseRequest;

class MessageInfoRequest extends ApiBaseRequest
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
        $param = [
            [
                "table" => "t_message",
                "condition" => [
                    "message_id" => $this->id
                ]
            ]
        ];
        $this->validateInputParamater($param);
        return [];
    }

    public function messages()
    {
        return [];
    }
}
