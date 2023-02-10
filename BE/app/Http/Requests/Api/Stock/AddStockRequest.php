<?php

namespace App\Http\Requests\Api\Stock;

use App\Http\Requests\Api\ApiBaseRequest;

class AddStockRequest extends ApiBaseRequest
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
            'facility_cd' => 'required|array',
            'facility_cd.*' => 'max:25',
            'person_cd' => 'nullable|array',
            'person_cd.*' => 'max:15',
            'stock_type' => 'nullable|max:3',
            'content_cd' => 'nullable|max:3',
            'action_id' => 'nullable|integer',
            'product_cd' => 'nullable|array',
            'product_cd.*' => 'max:10'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}
