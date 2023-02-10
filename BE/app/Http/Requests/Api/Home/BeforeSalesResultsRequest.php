<?php

namespace App\Http\Requests\Api\Home;

use App\Http\Requests\Api\ApiBaseRequest;

class BeforeSalesResultsRequest extends ApiBaseRequest
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
            'aggregate_layer_num' => 'nullable|integer'
        ];
    }

    public function messages()
    {
        return [];
    }
}
