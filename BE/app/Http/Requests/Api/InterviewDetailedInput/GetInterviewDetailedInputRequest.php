<?php

namespace App\Http\Requests\Api\InterviewDetailedInput;

use App\Http\Requests\Api\ApiBaseRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class GetInterviewDetailedInputRequest extends ApiBaseRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $param = [
            [
                "table" => "t_schedule",
                "condition" => [
                    "schedule_id" => $this->schedule_id
                ]
            ],
            [
                "table" => "t_call",
                "condition" => [
                    "call_id" => $this->call_id
                ]
            ]
        ];
        $this->validateInputParamater_($param);
        return [
            'call_id' => 'required',
            'schedule_id' => 'required',
        ];
    }

    public function messages() {
        return [
            'call_id' => [
                'required' => __('validation.required'),
            ],
            'schedule_id' => [
                'required' => __('validation.required'),
            ]
        ];
    }

    public function validateInputParamater_($param)
    {
        if(count($param) > 0){
            foreach($param as $item){
                // sql
                $sql = "SELECT COUNT(*) as total FROM ". $item['table'];
                $i = 0;
                if(count($item['condition']) > 0){
                    foreach($item['condition'] as $key => $condition){
                        if($i == 0){
                            $sql .= " WHERE CAST(". $key . " AS CHAR) = " . "'" .$condition ."'";
                        }else{
                            $sql .= " AND CAST(". $key . " AS CHAR) = " . "'" .$condition."'";
                        }
                        $i++;
                    }
                }
                $data =  DB::select($sql);
                $total = $data[0]->total ?? 0;
                if($total == 0){
                    throw new HttpResponseException($this->responseErrorValidate(config('messages.MSFA0181'), $param));
                }
            }
        }
        return true;
    }
}
