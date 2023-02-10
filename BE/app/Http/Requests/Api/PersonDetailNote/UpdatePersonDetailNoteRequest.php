<?php

namespace App\Http\Requests\Api\PersonDetailNote;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\Api\ApiBaseRequest;

class UpdatePersonDetailNoteRequest extends ApiBaseRequest
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
        $rules['person_consideration_confirm'] = ['nullable', function ($attr, $value, $fail) {
            if(!empty(trim($value))){
                $values = explode(',', trim($value));
                foreach ($values as $user_cd) {
                    if (!empty($user_cd)) {
                        $sql = "SELECT COUNT(*) AS countUser FROM m_user WHERE user_cd = :user_cd";
                        $result = DB::select($sql, [
                            'user_cd' => $user_cd
                        ]);
                        if ($result[0]->countUser == 0) {
                            return $fail(__('validation.exists'));
                        }
                    }else{
                        return $fail(__('validation.exists'));
                    }
                }
            }
        }];
        $rules['person_cd'] = ['required', 'max:15'];
        $rules['consideration_type'] = ['required', 'max:3'];
        $rules['person_consideration_id'] = ['required'];
        return $rules;
    }

    public function messages()
    {
        return [];
    }
}