<?php

namespace App\Http\Requests\Api\AttendantManagement;

use App\Http\Requests\Api\ApiBaseRequest;

class AddAttendantRequest extends ApiBaseRequest
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
            'convention_id' => 'required|max:20',
            'convention_attendee' => 'nullable',
            'convention_attendee.*.other_medical_staff_type' => 'nullable|max:15',
            'convention_attendee.*.person_name' => 'nullable|max:50',
            'convention_attendee.*.facility_cd' => 'required|max:25',
            'convention_attendee.*.facility_short_name' => 'nullable|max:50',
            'convention_attendee.*.facility_name_kana' => 'nullable|max:100',
            'convention_attendee.*.part_type' => 'nullable|max:2',
            'convention_attendee.*.display_position_cd' => 'nullable|max:3',
            'convention_attendee.*.display_position_name' => 'nullable|max:25',
            'convention_attendee.*.person_cd' => 'nullable|max:15',
            'convention_attendee.*.person_name_kana' => 'nullable|max:50',
            'convention_attendee.*.display_position_cd' => 'nullable|max:3',
            'convention_attendee.*.display_position_name' => 'nullable|max:25',
            'convention_attendee.*.other_person_flag' => 'required|boolean',
            'convention_attendee.*.convention_attendee_status_detail' => 'nullable|array',
            'convention_attendee.*.convention_attendee_status_detail.*.status_item_cd' => 'required|integer',
            'convention_attendee.*.convention_attendee_status_detail.*.status_item_value' => 'max:2',
            'other_attendee' => 'required|array'
        ];
    }

    public function messages()
    {
        return [];
    }
}
