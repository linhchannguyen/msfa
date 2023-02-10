<?php

namespace App\Http\Requests\Api\Inform;

use App\Http\Requests\Api\ApiBaseRequest;

class SearchRequest extends ApiBaseRequest
{
    protected $dateTimeFields = [
        "inform_datetime_from" => 'Y-m-d',
        "inform_datetime_to" => 'Y-m-d'
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
            'inform_datetime_from' => 'nullable|date:Y-mm-dd',
            'inform_datetime_to' => 'nullable|date:Y-mm-dd'
        ];
    }

    public function messages()
    {
        return [
            'inform_datetime_from' => [
                'date_format' => __('validation.date_format'),
            ],
            'inform_datetime_to' => [
                'date_format' => __('validation.date_format'),
            ]
        ];
    }
}
