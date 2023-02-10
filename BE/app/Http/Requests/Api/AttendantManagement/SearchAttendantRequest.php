<?php

namespace App\Http\Requests\Api\AttendantManagement;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Support\Facades\DB;

class SearchAttendantRequest extends ApiBaseRequest
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
            'convention_id' => 'required|exists:t_convention,convention_id',
            'attendance' => 'nullable|boolean',
            'unfollow' => 'nullable|boolean',
            'user_cd' => 'nullable|array'
        ];
    }

    public function messages()
    {
        return [];
    }
}
