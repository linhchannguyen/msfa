<?php

namespace App\Repositories\FacilityDetailsTime;

use App\Repositories\BaseRepository;
use App\Repositories\FacilityDetailsTime\FacilityDetailsTimeLineRepositoryInterface;

class FacilityDetailsTimeLineRepository extends BaseRepository implements FacilityDetailsTimeLineRepositoryInterface
{

    public function getScreenData()
    {
        $sql = "SELECT definition_label,definition_value FROM m_variable_definition WHERE definition_name = :definition_name ORDER BY sort_order ASC;";
        return $this->_find($sql, ['definition_name' => 'チャネル']);
    }

    public function getFacilityDetailsTimeLine($facility_cd, $person_cd, $product_cd, $start_datetime, $end_datetime, $channel_type, $timeline_id)
    {
        $query = [];
        $sql = "
            SELECT 
                t_timeline.start_datetime,
                CONCAT( '[', GROUP_CONCAT( JSON_OBJECT(
                                'channel_type', t_timeline.channel_type,
                                'channel_id', t_timeline.channel_id,
                                'timeline_id', t_timeline.timeline_id
                            ) 
                        ), 
                    ']' 
            ) as content
            FROM t_timeline
            INNER JOIN m_variable_definition
                 ON (m_variable_definition.definition_value = t_timeline.channel_type AND m_variable_definition.definition_name = 'チャネル')
            WHERE 1 = 1 ";
            
        if (!empty($facility_cd)) {
            $sql .= " AND t_timeline.facility_cd = :facility_cd ";
            $query['facility_cd'] = $facility_cd;
        }

        if (!empty($product_cd)) {
            $product_cd = explode(',', $product_cd);
            $sql .= " AND (";
            foreach($product_cd as $key => $value){
                $sql .= "t_timeline.product_name LIKE CONCAT('%',(SELECT product_name FROM m_product WHERE product_cd = :product_cd_".$key."),'%') OR ";
                $query['product_cd_'.$key] = $value;
            }
            $sql = rtrim($sql, 'OR ');
            $sql .= ") ";
        }

        if (!empty($person_cd)) {
            $person_cd = explode(',', $person_cd);
            $sql .= " AND t_timeline.person_cd IN " . $this->_buildCommaString($person_cd, true);
        }

        if (!empty($start_datetime) && !empty($end_datetime)) {
            $sql .= " AND  DATE_FORMAT(t_timeline.start_datetime,'%Y/%m/%d')  BETWEEN  DATE_FORMAT(:start_datetime,'%Y/%m/%d') AND  DATE_FORMAT(:end_datetime,'%Y/%m/%d') ";
            $query['start_datetime'] = $start_datetime;
            $query['end_datetime'] = $end_datetime;
        }

        if (!empty($channel_type)) {
            $channel_type = explode(',', $channel_type);
            $sql .= " AND t_timeline.channel_type IN " . $this->_buildCommaString($channel_type, true);
        }

        if (!empty($timeline_id)) {
            $sql .= " AND t_timeline.timeline_id IN " . $this->_buildCommaString($timeline_id, true);
        }

        $sql .= " GROUP BY CAST(t_timeline.start_datetime as DATE) ORDER BY t_timeline.start_datetime DESC , t_timeline.timeline_id DESC;";
        return $this->_find($sql, $query);
    }

    public function getChannel10Detail($facility_cd,$start_datetime,$channel_type,$channel_id,$timeline_id=null)
    {
        $condition = [];
        $sql = "
        SELECT 
            t_timeline.start_datetime,
            t_timeline.end_datetime,
            t_timeline.channel_type,
            m_variable_definition.definition_label,
            t_timeline.channel_id,
            t_timeline.channel_id as call_id,
            t_visit.schedule_id,
            (SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT(
                            'channel_detail_id', t_timeline.channel_detail_id,
                            'product_name', t_timeline.product_name,
                            'note', t_timeline.note,
                            'content_name', t_timeline.content_name,
                            'phase_name', t_timeline.phase_name,
                            'reaction_name', t_timeline.reaction_name,
                            'detail_order', t_timeline.detail_order
                        ) 
                    ), 
                ']' 
            ) FROM t_timeline AS tt
              WHERE tt.timeline_id = t_timeline.timeline_id
            )as channel_detail,
            t_timeline.off_label_flag,
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
            t_timeline.display_position_name
         FROM t_timeline
         INNER JOIN m_variable_definition
                ON (m_variable_definition.definition_value = t_timeline.channel_type AND m_variable_definition.definition_name = 'チャネル')
         INNER JOIN t_call
                ON t_call.call_id = t_timeline.channel_id
         INNER JOIN t_visit
                ON t_visit.visit_id = t_call.visit_id
         WHERE 1 = 1";
        if(!empty($timeline_id)){
            $sql .= " AND t_timeline.channel_type = :channel_type AND t_timeline.timeline_id IN " . $this->_buildCommaString($timeline_id, true);
            $condition['channel_type'] = $channel_type;
        }else{
            $sql .= " AND CAST(t_timeline.start_datetime AS DATE) = :start_datetime
            AND t_timeline.channel_type = :channel_type
            AND t_timeline.channel_id = :channel_id
            AND t_timeline.facility_cd = :facility_cd ";
            $condition['start_datetime'] = date('Y-m-d', strtotime($start_datetime));
            $condition['channel_type'] = $channel_type;
            $condition['channel_id'] = $channel_id;
            $condition['facility_cd'] = $facility_cd;
        }

        $sql .= " GROUP BY t_timeline.channel_id , t_timeline.channel_type ORDER BY t_timeline.start_datetime DESC , t_timeline.timeline_id DESC;";

        return $this->_first($sql, $condition);
    }

    public function getChannel20Detail($facility_cd,$start_datetime,$channel_type,$channel_id,$timeline_id=null)
    {
        $condition = [];
        $sql = "
        SELECT 
            t_timeline.start_datetime,
            t_timeline.end_datetime,
            t_timeline.off_label_flag,
            t_timeline.channel_type,
            t_timeline.channel_id,
            t_timeline.channel_id as call_id,
            t_visit.schedule_id,
            t_briefing.briefing_id,
            m_variable_definition.definition_label,
            t_timeline.channel_detail_id,
            t_timeline.org_label,
            t_timeline.user_cd,
            t_timeline.user_name,
            t_timeline.user_post_type,
            t_timeline.title,
            t_timeline.facility_cd,
            t_timeline.facility_short_name,
            t_timeline.department_name,
            t_timeline.product_name,
            t_timeline.note,
            t_timeline.briefing_type_name,
            t_timeline.briefing_facility_short_name
         FROM t_timeline
         INNER JOIN m_variable_definition
                 ON (m_variable_definition.definition_value = t_timeline.channel_type AND m_variable_definition.definition_name = 'チャネル')
         LEFT JOIN t_call
                 ON t_call.call_id = t_timeline.channel_id
         LEFT JOIN t_visit
                 ON t_visit.visit_id = t_call.visit_id
         INNER JOIN t_briefing
                 ON t_briefing.briefing_id = t_timeline.channel_id
         WHERE 1 = 1";
        if(!empty($timeline_id)){
            $sql .= " AND t_timeline.channel_type = :channel_type AND t_timeline.timeline_id IN " . $this->_buildCommaString($timeline_id, true);
            $condition['channel_type'] = $channel_type;
        }else{
            $sql .= " AND t_timeline.start_datetime = :start_datetime
            AND t_timeline.channel_type = :channel_type
            AND t_timeline.channel_id = :channel_id
            AND t_timeline.facility_cd = :facility_cd";
            $condition['start_datetime'] = $start_datetime;
            $condition['channel_type'] = $channel_type;
            $condition['channel_id'] = $channel_id;
            $condition['facility_cd'] = $facility_cd;
        }

        $sql .= " GROUP BY t_timeline.channel_id ORDER BY t_timeline.start_datetime DESC , t_timeline.timeline_id DESC;";

        return $this->_find($sql, $condition);
    }

    public function getChannel30Detail($facility_cd,$start_datetime,$channel_type,$channel_id,$timeline_id=null)
    {
        $condition = [];
        $sql = "
        SELECT 
            t_timeline.start_datetime,
            t_timeline.end_datetime,
            t_timeline.off_label_flag,
            t_timeline.channel_type,
            t_timeline.channel_id,
            t_timeline.channel_id as call_id,
            t_visit.schedule_id,
            t_convention.convention_id,
            m_variable_definition.definition_label,
            t_timeline.channel_detail_id,
            t_timeline.org_label,
            t_timeline.user_cd,
            t_timeline.user_name,
            t_timeline.user_post_type,
            t_timeline.title,
            t_timeline.facility_cd,
            t_timeline.facility_short_name,
            t_timeline.department_name,
            t_timeline.product_name,
            t_timeline.note,
            t_timeline.convention_type_name
         FROM t_timeline
         INNER JOIN m_variable_definition
                 ON (m_variable_definition.definition_value = t_timeline.channel_type AND m_variable_definition.definition_name = 'チャネル')
         LEFT JOIN t_call
                 ON t_call.call_id = t_timeline.channel_id
         LEFT JOIN t_visit
                 ON t_visit.visit_id = t_call.visit_id
         INNER JOIN t_convention
                 ON t_convention.convention_id = t_timeline.channel_id
         WHERE 1 = 1";
        if(!empty($timeline_id)){
            $sql .= " AND t_timeline.channel_type = :channel_type AND t_timeline.timeline_id IN " . $this->_buildCommaString($timeline_id, true);
            $condition['channel_type'] = $channel_type;
        }else{
            $sql .= " AND t_timeline.start_datetime = :start_datetime
            AND t_timeline.channel_type = :channel_type
            AND t_timeline.channel_id = :channel_id
            AND t_timeline.facility_cd = :facility_cd";
            $condition['start_datetime'] = $start_datetime;
            $condition['channel_type'] = $channel_type;
            $condition['channel_id'] = $channel_id;
            $condition['facility_cd'] = $facility_cd;
        }
        $sql .= " GROUP BY t_timeline.channel_id ORDER BY t_timeline.start_datetime DESC , t_timeline.timeline_id DESC;";

        return $this->_find($sql, $condition);
        
    }

    public function getChannelDetail($facility_cd,$start_datetime,$channel_type,$channel_id, $timeline_id=null)
    {
        $condition = [];
        $sql = "
        SELECT 
            t_timeline.start_datetime,
            t_timeline.end_datetime,
            t_timeline.channel_type,
            t_timeline.channel_id,
            t_timeline.channel_id as call_id,
            t_visit.schedule_id,
            m_variable_definition.definition_label,
            t_timeline.channel_detail_id,
            t_timeline.product_name,
            t_timeline.content_name,
            t_timeline.phase_name,
            t_timeline.reaction_name,
            t_timeline.detail_order,
            t_timeline.org_label,
            t_timeline.user_cd,
            t_timeline.user_name,
            t_timeline.user_post_type,
            t_timeline.off_label_flag,
            t_timeline.title,
            t_timeline.facility_cd,
            t_timeline.facility_short_name,
            t_timeline.department_name,
            t_timeline.person_cd,
            t_timeline.person_name,
            t_timeline.display_position_name,
            t_timeline.note
         FROM t_timeline
         JOIN m_variable_definition
                 ON (m_variable_definition.definition_value = t_timeline.channel_type AND m_variable_definition.definition_name = 'チャネル')
         LEFT JOIN t_call
                ON t_call.call_id = t_timeline.channel_id
         LEFT JOIN t_visit
                ON t_visit.visit_id = t_call.visit_id
         WHERE 1 = 1";
        if(!empty($timeline_id)){
            $sql .= " AND t_timeline.channel_type = :channel_type AND t_timeline.timeline_id IN " . $this->_buildCommaString($timeline_id, true);
            $condition['channel_type'] = $channel_type;
        }else{
            $sql .= " AND t_timeline.start_datetime = :start_datetime
            AND t_timeline.channel_type = :channel_type
            AND t_timeline.channel_id = :channel_id
            AND t_timeline.facility_cd = :facility_cd";
            $condition['start_datetime'] = $start_datetime;
            $condition['channel_type'] = $channel_type;
            $condition['channel_id'] = $channel_id;
            $condition['facility_cd'] = $facility_cd;
        }

        $sql .= " GROUP BY t_timeline.channel_id ORDER BY t_timeline.start_datetime DESC , t_timeline.timeline_id DESC;";

        return $this->_find($sql, $condition);
    }
}
