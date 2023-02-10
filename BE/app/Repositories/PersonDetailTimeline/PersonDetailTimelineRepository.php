<?php

namespace App\Repositories\PersonDetailTimeline;

use App\Repositories\BaseRepository;
use App\Traits\StringConvertTrait;

class PersonDetailTimelineRepository extends BaseRepository implements PersonDetailTimelineRepositoryInterface
{
    use StringConvertTrait;
    public function getScreenData()
    {
        $sql = "SELECT definition_label,definition_value FROM m_variable_definition WHERE definition_name LIKE :definition_name ORDER BY sort_order ASC;";
        return $this->_find($sql, ['definition_name' => trim($this->convert_kana(TIMELINE_CHANEL_DIVISION))]);
    }

    public function searchTimeline($conditions)
    {
        $sql = "SELECT
            (SELECT t_visit.schedule_id 
                FROM t_call
                JOIN t_visit ON t_visit.visit_id = t_call.visit_id
                WHERE t_call.call_id = t_timeline.channel_id LIMIT 1
            ) as schedule_id,
            t_timeline.start_datetime,
            CAST(t_timeline.start_datetime AS DATE) as start_date,
            t_timeline.end_datetime,
            t_timeline.channel_type,
            t_timeline.channel_id,
            t_timeline.channel_detail_id,
            t_timeline.product_name,
            t_timeline.note,
            t_timeline.off_label_flag,
            t_timeline.content_name,
            t_timeline.phase_name,
            t_timeline.reaction_name,
            t_timeline.detail_order,
            t_timeline.org_label,
            t_timeline.user_cd,
            t_timeline.user_name,
            t_timeline.user_post_type,
            t_timeline.title,
            t_timeline.facility_cd,
            t_timeline.facility_short_name,
            t_timeline.department_name,
            t_timeline.person_cd,
            t_timeline.person_name,
            t_timeline.display_position_name,
            t_timeline.convention_type_name,
            t_timeline.briefing_type_name,
            t_timeline.briefing_facility_short_name,
            t_timeline.content
        FROM t_timeline
        WHERE t_timeline.person_cd = :person_cd ";
        $condition['person_cd'] = $conditions['person_cd'];
        $search_product_name = '';
        if (!empty($conditions['product_name'])) {
            $product_names = explode(',', trim($conditions['product_name']));
            foreach ($product_names as $key => $product_name) {
                $search_product_name .= " OR t_timeline.product_name LIKE :product_name_$key";
                $condition['product_name_' . $key] = '%' . trim($this->convert_kana($product_name)) . '%';
            }
            $search_product_name = " AND (" . substr($search_product_name, 3) . " )";
            $sql .= $search_product_name;
        }
        if (!empty($conditions['start_datetime'])) {
            $sql .= " AND CAST(t_timeline.start_datetime AS DATE) >= :start_datetime";
            $condition['start_datetime'] = $conditions['start_datetime'];
        }
        if (!empty($conditions['end_datetime'])) {
            $sql .= " AND CAST(t_timeline.end_datetime AS DATE) <= :end_datetime";
            $condition['end_datetime'] = $conditions['end_datetime'];
        }
        $channel_types = [];
        if (!empty($conditions['channel_type'])) {
            $channel_type = explode(',', $conditions['channel_type']);
            foreach ($channel_type as $value) {
                if (!empty($value)) {
                    array_push($channel_types, $value);
                }
            }
        }
        if (!empty($channel_types)) {
            $sql .= " AND t_timeline.channel_type IN " . $this->_buildCommaString($channel_types, true);
        }
        $sql .= " ORDER BY CAST(t_timeline.start_datetime AS DATE) DESC";
        return $this->_find($sql, $condition);
    }

    public function getDetailByCallID($call_id)
    {
        $sql = "SELECT channel_detail_id,
                       detail_order,
                       content_name,
                       product_name,
                       phase_name,
                       reaction_name,
                       note
                FROM t_timeline
                WHERE channel_id = :call_id
                ORDER BY detail_order";
        return $this->_find($sql, ['call_id' => $call_id]);
    }
}
