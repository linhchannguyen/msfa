<?php

namespace App\Http\Requests\Api\Schedule;

use App\Http\Requests\Api\ApiBaseRequest;

class SearchStockRequest extends ApiBaseRequest
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
        return [
            'status_type' => 'required|boolean'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
