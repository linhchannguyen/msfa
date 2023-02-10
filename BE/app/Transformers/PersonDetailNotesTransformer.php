<?php

namespace App\Transformers;

use App\Traits\DateTimeTrait;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonDetailNotesTransformer extends JsonResource
{
    use DateTimeTrait;
    public function toArray($request)
    {
        $user_confirm_list = json_decode($this->user_confirm_list, true);
        return [
            'person_consideration_id' => $this->person_consideration_id,
            'person_cd' => $this->person_cd,
            'consideration_type' => $this->consideration_type,
            'consideration_type_name' => $this->consideration_type_name,
            'consideration_contents' => $this->consideration_contents,
            'last_update_datetime' => $this->responseDateTimeFormat($this->last_update_datetime, 'Y/m/d'),
            'create_user_cd' => $this->create_user_cd,
            'create_user_name' => $this->create_user_name,
            'create_org_cd' => $this->create_org_cd,
            'create_org_label' => $this->create_org_label,
            'updated_by' => $this->updated_by,
            'user_confirm_list' => $this->convertToArray($user_confirm_list)
        ];
    }

    public function convertToArray($array)
    {
        $array = empty($array) ? [] : $array;
        $result = [];
        foreach($array as $val){
            $val['confirmed_datetime'] = $this->responseDateTimeFormat($val['confirmed_datetime'], 'Y/m/d H:i:s');
            $result[] = $val;
        }
        return $result;
    }
}