<?php

namespace App\Http\Requests\Api\Home;

use App\Http\Requests\Api\ApiBaseRequest;

class ActualDigestionRankingRequest extends ApiBaseRequest
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
            'aggregate_layer_num' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [];
    }
}
