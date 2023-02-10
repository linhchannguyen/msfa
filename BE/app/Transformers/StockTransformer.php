<?php

namespace App\Transformers;

use App\Traits\DateTimeTrait;

use Illuminate\Http\Resources\Json\JsonResource;

class StockTransformer extends JsonResource
{

    use DateTimeTrait;
    public function toArray($request)
    {
        return [
            'stock_id' => $this->stock_id,
            'product_list' => $this->convertToArray(explode(',', $this->product_cd_list), explode(',', $this->product_name_list), [] , [], ['product_cd', 'product_name']),
            'document_list' => $this->convertToArray(explode(',', $this->document_id_list), explode(',', $this->document_name_list), explode(',', $this->file_type_list), explode(',', $this->document_type_list), ['document_id', 'document_name', 'file_type','document_type']),
            'facility_cd' => $this->facility_cd,
            'facility_category_type' => $this->facility_category_type,
            'facility_name' => $this->facility_name,
            'facility_short_name' => $this->facility_short_name,
            'department_cd' => $this->department_cd,
            'department_name' => $this->department_name,
            'person_cd' => $this->person_cd,
            'person_name' => $this->person_name,
            'content_cd' => $this->content_cd,
            'content_name' => $this->content_name,
            'status_type' => $this->status_type,
            'stock_type' => $this->stock_type,
            'action_id' => $this->action_id,
            'stock_date' => $this->responseDateTimeFormat($this->stock_date, 'Y/m/d')
        ];
    }

    public function convertToArray($data1, $data2, $data3, $data4, $keys = [])
    {
        $results = [];
        if(count($data1) >= 1 && count($data2) >= 1){
            foreach($data1 as $index => $val){
                if($val == ""  || empty($val)){
                    break;
                }
                $arr_tmp = [];
                $arr_tmp[$keys[0]] = $val;
                $arr_tmp[$keys[1]] = $data2[$index];
                if(!empty($data3)){
                    $arr_tmp[$keys[2]] = $data3[$index];
                }
                if(!empty($data4)){
                    $arr_tmp[$keys[3]] = $data4[$index];
                }
                array_push($results, $arr_tmp);
            }
        }
        return $results;
    }
}
