<?php

namespace App\Http\Requests\Api\UserColor;

use App\Http\Requests\Api\ApiBaseRequest;

class GetWatchUserRequest extends ApiBaseRequest
{
    protected $dateTimeFields = [
        "startDate" => 'Y-m-d',
        "endDate" => 'Y-m-d'
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
            'startDate' => 'required|date:Y-mm-dd',
            'endDate' => 'required|date:Y-mm-dd|after_or_equal:startDate'
        ];
    }
    public function messages()
    {
        return [
        ];
    }
}
