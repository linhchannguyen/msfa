<?php

namespace App\Http\Requests\Api\Schedule;

use App\Http\Requests\Api\ApiBaseRequest;

class GetScreenDataRequest extends ApiBaseRequest
{
    protected $dateTimeFields = [
        "start_date" => 'Y-m-d',
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
            'start_date' => 'nullable|date:Y-mm-dd'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
