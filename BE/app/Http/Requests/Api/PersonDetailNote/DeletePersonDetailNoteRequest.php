<?php

namespace App\Http\Requests\Api\PersonDetailNote;

use App\Http\Requests\Api\ApiBaseRequest;

class DeletePersonDetailNoteRequest extends ApiBaseRequest
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
            'person_consideration_id' => 'required|exists:t_person_consideration,person_consideration_id'
        ];
    }

    public function messages()
    {
        return [
        ];
    }
}