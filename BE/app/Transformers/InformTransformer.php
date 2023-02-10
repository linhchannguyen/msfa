<?php

namespace App\Transformers;

use App\Traits\DateTimeTrait;
use App\Enums\InformUrl;

use Illuminate\Http\Resources\Json\JsonResource;

class InformTransformer extends JsonResource
{

    use DateTimeTrait;
    public function toArray($request)
    {
        $url = InformUrl::describe($this->parameter_key);
        $param = "";
        if ($this->parameter_key == PERSON_CD) {
            $param = '?tab=4';
            $url .= $this->parameter_value . $param;
        } else if ($this->parameter_key == 'schedule_id') {
            $param = '?schedule_id=';
            $url .= $param . $this->parameter_value;
        } else if ($this->parameter_key == KNOWLEDGE_ID) {
            $param = '?' . KNOWLEDGE_ID . '=';
            $url .= $param . $this->parameter_value;
        } else if ($this->parameter_key == 'facility_cd') {
            $param = '?tab=3';
            $url .= $this->parameter_value . $param;
        } else if ($this->parameter_key == 'convention_id') {
            $param = '?convention_id=';
            $url .= $param . $this->parameter_value;
        } else if ($this->parameter_key == 'report_id') {
            $url .= $this->parameter_value;
        } else if ($this->parameter_key == 'briefing_id') {
            $url .= $this->parameter_value;
        } else if ($this->parameter_key == 'calendar') {
        } else if ($this->parameter_key == 'knowledge_id') {
            $url .= '?knowledge_id=' . $this->parameter_value;
        }
        return [
            'inform_id' => $this->inform_id,
            'inform_category_cd' => $this->inform_category_cd,
            'inform_category_name' => $this->inform_category_name,
            'inform_datetime' => $this->responseDateTimeFormat($this->inform_datetime, 'Y/m/d H:i:s'),
            'inform_user_cd' => $this->inform_user_cd,
            'inform_contents' => $this->inform_contents,
            'archive_flag' => $this->archive_flag,
            'informed_flag' => $this->informed_flag,
            'read_flag' => $this->read_flag,
            'parameter_key' => $this->parameter_key,
            'parameter_value' => $this->parameter_value,
            'url' => $url,
        ];
    }
}
