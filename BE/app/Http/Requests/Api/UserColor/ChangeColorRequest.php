<?php

namespace App\Http\Requests\Api\UserColor;

use App\Http\Requests\Api\ApiBaseRequest;
use App\Traits\AuthTrait;
use Illuminate\Support\Facades\DB;

class ChangeColorRequest extends ApiBaseRequest
{
    use AuthTrait;
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
        $rules['user_cd'] = ['required', function ($attr, $value, $fail) {
            // get value user_cd
            $watch_user_cds = [];
            if (is_array($value)) {
                foreach ($value as $user_cd) {
                    if (!empty($user_cd)) {
                        array_push($watch_user_cds, $user_cd);
                    }
                }
            }
            if (!empty($watch_user_cds)) {
                if(count($watch_user_cds) > 1){//Create Mode
                    $sql = "SELECT COUNT(*) AS countUser FROM t_watch_user WHERE user_cd = :user_cd AND watch_user_cd IN " . $this->_buildCommaString($watch_user_cds, true);
                    $result = DB::select($sql, [
                        'user_cd' => $this->getCurrentUser()
                    ]);
                    if ($result[0]->countUser != 0) {
                        return $fail(__('validation.unique'));
                    }
                }
            }
        }];
        $rules['display_color_cd'] = ['required'];
        $rules['display_flag'] = ['required', 'boolean'];
        return $rules;
    }
    public function messages()
    {
        return [];
    }

    public function _buildCommaString($data, $withBrackets = false)
    {
        $string = '';

        foreach ($data as $param) {
            if (is_string($param)) {
                $string .= "'$param',";
            } else {
                $string .= "$param,";
            }
        }

        $string = trim($string, ',');

        if ($withBrackets) {
            $string = "($string)";
        }

        return $string;
    }
}
