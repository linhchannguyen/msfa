<?php

namespace App\Repositories\TimelineSearch;

use App\Repositories\BaseRepository;
use App\Repositories\TimelineSearch\TimelineSearchRepositoryInterface;

class TimelineSearchRepository extends BaseRepository implements TimelineSearchRepositoryInterface
{
    protected $useAutoMeta = true;
    private $approval_result;
    public function __construct()
    {
        $this->channel = "チャネル";
        $this->approval_result = 60;
    }

    public function getChannel()
    {
        $query['definition_name'] = $this->channel;
        $sql = "SELECT definition_value as channel_value,definition_label as channel_label FROM m_variable_definition WHERE definition_name = :definition_name";
        return $this->_find($sql, $query);
    }

    public function getListDataTimeline($request)
    {
        $where = "";
        $query = [];
        // filter user_cd
        $user_cd = [];
        if ($request->user_cd) {
            $user_cd = explode(',', $request->user_cd);
        }
        if (count($user_cd) > 0) {
            $where .= " AND t_timeline.user_cd IN " . $this->_buildCommaString($user_cd, true);
        }
        // filter channel_type
        $channel_type = [];
        if ($request->channel_type) {
            $channel_type = explode(',', $request->channel_type);
        }
        if (count($channel_type) > 0) {
            $where .= " AND channel_type IN " . $this->_buildCommaString($channel_type, true);
        }
        // filter facility_cd
        $facility_cd = [];
        if ($request->facility_cd) {
            $facility_cd = explode(',', $request->facility_cd);
        }
        if (count($facility_cd) > 0) {
            $where .= " AND facility_cd IN " . $this->_buildCommaString($facility_cd, true);
        }
        // filter facility_cd
        $person_cd = [];
        if ($request->person_cd) {
            $person_cd = explode(',', $request->person_cd);
        }
        if (count($person_cd) > 0) {
            $where .= " AND person_cd IN " . $this->_buildCommaString($person_cd, true);
        }
        // filter product_cd
        $product_name = [];
        if ($request->product_name) {
            $product_name = explode(',', $request->product_name);
        }
        if (count($product_name) > 0) {
            $where .= " AND t_timeline.product_name IN " . $this->_buildCommaString($product_name, true);
        }
        if ($request->start_datetime) {
            $where .= " AND CAST(t_timeline.start_datetime AS DATE) >= :start_datetime";
            $query['start_datetime'] = $request->start_datetime;
        }
        if ($request->end_datetime) {
            $where .= " AND CAST(t_timeline.end_datetime AS DATE) <= :end_datetime";
            $query['end_datetime'] = $request->end_datetime;
        }
        $query['status_type_briefing'] = $query['status_type_convention'] = $this->approval_result;
        $sql = "SELECT 
                (SELECT t_visit.schedule_id 
                    FROM t_call
                    JOIN t_visit ON t_visit.visit_id = t_call.visit_id
                    WHERE t_call.call_id = t_timeline.channel_id LIMIT 1
                ) as schedule_id,
                t_timeline.timeline_id,
                t_timeline.start_datetime,
                CAST(t_timeline.start_datetime AS DATE) as start_date,
                t_timeline.end_datetime,
                t_timeline.channel_type, 
                t_timeline.channel_id, 
                (CASE WHEN (SELECT briefing_id FROM t_briefing WHERE briefing_id = t_timeline.channel_id AND status_type = :status_type_briefing LIMIT 1) IS NULL THEN 0 ELSE 1 END) as briefing_id,
                (CASE WHEN (SELECT convention_id FROM t_convention WHERE convention_id = t_timeline.channel_id AND status_type = :status_type_convention LIMIT 1) IS NULL THEN 0 ELSE 1 END) as convention_id,
                t_timeline.channel_detail_id, 
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
                t_timeline.product_name, 
                t_timeline.note, 
                t_timeline.off_label_flag, 
                t_timeline.content_name,
                t_timeline.phase_name,
                t_timeline.reaction_name,
                t_timeline.detail_order,
                t_timeline.convention_type_name, 
                t_timeline.briefing_type_name, 
                t_timeline.briefing_facility_short_name, 
                t_timeline.content
                FROM t_timeline
                WHERE 1 = 1 " . $where . " ORDER BY CAST(t_timeline.start_datetime AS DATE) DESC";
        return $this->_find($sql, $query);
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
