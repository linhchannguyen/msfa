<?php

namespace App\Http\Requests\Api\Message;

use App\Http\Requests\Api\ApiBaseRequest;

class MessageRequest extends ApiBaseRequest
{
    protected $dateTimeFields = [
        "release_start_date" => 'Y-m-d',
        "release_end_date" => 'Y-m-d'
    ];
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
            'message_subject' => 'required|string|max:30',
            'release_start_date' => 'required|date:Y-mm-dd',
            'release_end_date' => 'required|date:Y-mm-dd|after_or_equal:release_start_date',
            'sender_name' => 'required|string|max:50',
            'important_flag' => 'required|boolean',
            'release_org_cd' => 'nullable|string|max:20',
            'message_contents' => 'required',
        ];
    }

    public function messages()
    {
        return [];
    }
}
