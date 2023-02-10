<?php

namespace App\Traits;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\DB;

trait ValidateRequestTrait
{
    use ApiResponseTrait;

    public function validateInputParamater($param)
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
                    throw new HttpResponseException($this->responseNotFoundValidate(config('messages.MSFA0181'), $param));
                }
            }
        }
        return true;
    }
}
