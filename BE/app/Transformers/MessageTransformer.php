<?php

namespace App\Transformers;

use App\Traits\DateTimeTrait;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageTransformer extends JsonResource
{
    use DateTimeTrait;
    public function toArray($request)
    {
        return [
            'message_id' => $this->message_id,
            'message_subject' => $this->message_subject,
            'release_start_date' => $this->responseDateTimeFormat($this->release_start_date, 'Y/m/d'),
            'release_end_date' => $this->responseDateTimeFormat($this->release_end_date, 'Y/m/d'),
            'release_org_cd' => $this->release_org_cd,
            'org_name' => $this->org_name,
            'org_label' => $this->org_label,
            'sender_name' => $this->sender_name,
            'message_contents' => $this->message_contents,
            'important_flag' => $this->important_flag,
            'last_update_datetime' => $this->responseDateTimeFormat($this->last_update_datetime, 'Y/m/d H:i:s'),
            'create_user_cd' => $this->create_user_cd
        ];
    }
}
