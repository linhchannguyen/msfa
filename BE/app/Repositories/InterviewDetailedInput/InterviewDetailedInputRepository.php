<?php

namespace App\Repositories\InterviewDetailedInput;

use App\Repositories\BaseRepository;

class InterviewDetailedInputRepository extends BaseRepository implements InterviewDetailedInputRepositoryInterface
{
    protected $useAutoMeta = true;

    public function getScreenData()
    {
        $sql = "SELECT content_cd,content_name FROM m_content ORDER BY content_cd ASC;";
        $data['interviewContent'] = $this->_find($sql, []);

        $sql = "SELECT reaction_cd,reaction_name FROM m_reaction ORDER BY reaction_cd ASC;";
        $data['reaction'] =  $this->_find($sql, []);

        $sql = "SELECT phase_cd,phase_name FROM m_phase ORDER BY phase_cd ASC;";
        $data['phase'] =  $this->_find($sql, []);

        $sql = "SELECT definition_label,definition_value FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label= :definition_label;";
        $data['m_variable_definition'] =  $this->_first($sql, [ 'definition_name' => '資材使用機能区分' ,  'definition_label' => '面談' ]);

        return $data;
    }

    public function getInterviewDateTime($call_id, $schedule_id)
    {
        $sql = "
            SELECT 
                DATE_FORMAT(t_schedule.start_date,'%Y/%m/%d') as start_date ,
                DATE_FORMAT(t_schedule.start_time,'%Y/%m/%d %H:%i:%s') as start_time ,
                DATE_FORMAT(t_schedule.end_time,'%Y/%m/%d %H:%i:%s') as end_time,
                t_schedule.user_cd,
                t_visit.facility_short_name,
                t_visit.facility_cd,
                t_call.person_name,
                t_call.person_cd,
                t_call.call_id,
                t_call.off_label_flag,
                t_call.act_flag,
                t_visit.visit_id,
                t_report_detail.report_id,
                t_call.created_by,
                t_call.created_at,
                t_call.updated_at
            FROM t_schedule
            INNER JOIN t_visit
                ON (t_visit.schedule_id = t_schedule.schedule_id AND t_visit.schedule_id = :v_schedule_id)
            INNER JOIN t_call
                ON (t_call.visit_id = t_visit.visit_id AND t_call.call_id = :call_id)
            LEFT JOIN t_report_detail
                ON (t_report_detail.report_detail_type  = t_schedule.schedule_type AND t_report_detail.report_detail_id = t_schedule.schedule_id)
            WHERE t_schedule.schedule_id = :schedule_id
            GROUP BY t_schedule.start_date,t_schedule.start_time,t_schedule.end_time,t_visit.facility_short_name,t_call.person_name;";

        return $this->_first($sql, ['v_schedule_id' => $schedule_id, 'schedule_id' => $schedule_id, 'call_id' => $call_id]);
    }

    public function getOffLabel($call_id, $schedule_id)
    {
        $sql = "
            SELECT 
                t_call.off_label_concent_flag,
                t_call.related_product,
                t_call.question,
                t_call.answer,
                t_call.re_question,
                t_call.literature,
                t_call.off_label_call_time
            FROM t_call
            INNER JOIN t_visit
                ON (t_call.visit_id = t_visit.visit_id AND t_visit.schedule_id = :v_schedule_id)
            INNER JOIN t_schedule
                ON (t_visit.schedule_id = t_schedule.schedule_id AND t_schedule.schedule_id = :schedule_id)
            WHERE t_call.call_id = :call_id;";

        return $this->_first($sql, ['call_id' => $call_id, 'v_schedule_id' => $schedule_id, 'schedule_id' => $schedule_id]);
    }

    public function detailArea($call_id, $schedule_id)
    {
        $sql = "
            SELECT 
                t_detail.detail_order,
                t_detail.content_cd,
                t_detail.content_name_tran,
                t_detail.product_cd,
                t_detail.product_name,
                t_detail.detail_id,
                t_detail.reaction,
                t_detail.reaction_cd,
                t_detail.phase,
                t_detail.phase_cd,
                t_detail.prescription_count,
                t_detail.note,
                t_detail.remarks,
                (SELECT
                      CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                 'document_id', CAST(t_detail_document.document_id AS UNSIGNED ),
                                 'document_name' , t_document.document_name,
                                 'start_date' , t_document.start_date,
                                 'end_date' , t_document.end_date,
                                 'detail_id' , t_detail.detail_id
                             ) 
                         ), 
                     ']' 
                    )
                    FROM t_detail_document
                    INNER JOIN t_document
                         ON t_document.document_id = t_detail_document.document_id
                    WHERE t_detail_document.detail_id = t_detail.detail_id
                ) AS materials
            FROM t_detail
            INNER JOIN t_call
                ON t_detail.call_id = t_call.call_id
            INNER JOIN t_visit
                ON (t_call.visit_id = t_visit.visit_id AND t_visit.schedule_id = :v_schedule_id)
            INNER JOIN t_schedule
                ON (t_visit.schedule_id = t_schedule.schedule_id AND t_schedule.schedule_id = :schedule_id)
            WHERE t_detail.call_id = :call_id 
            ORDER BY t_detail.detail_order ASC;";

        return $this->_find($sql, ['call_id' => $call_id, 'v_schedule_id' => $schedule_id, 'schedule_id' => $schedule_id]);
    }

    public function getDetailArea($detail_id)
    {
        $sql = "
            SELECT 
                t_detail.detail_order,
                t_detail.content_cd,
                t_detail.content_name_tran,
                t_detail.product_cd,
                t_detail.product_name,
                t_detail.detail_id,
                t_detail.reaction,
                t_detail.reaction_cd,
                t_detail.phase,
                t_detail.phase_cd,
                t_detail.prescription_count,
                t_detail.note,
                t_detail.remarks,
                CONCAT( '[', GROUP_CONCAT( JSON_OBJECT( 
                                            'document_id', CAST(t_detail_document.document_id AS UNSIGNED ),
                                            'document_name' , t_document.document_name,
                                            'start_date' , t_document.start_date,
                                            'end_date' , t_document.end_date,
                                            'detail_id' , t_detail.detail_id
                                        ) 
                                    ), 
                                ']' 
                ) as materials
            FROM t_detail
            LEFT JOIN t_detail_document
                ON t_detail_document.detail_id = t_detail.detail_id
            LEFT JOIN t_document
                ON t_document.document_id = t_detail_document.document_id
            WHERE t_detail.detail_id = :detail_id
            GROUP BY t_detail.content_cd,t_detail.product_cd,t_detail.detail_id; ";

        return $this->_first($sql, ['detail_id' => $detail_id]);
    }

    public function updateSchedule($schedule_id, $display_option_type)
    {
        $sql = "UPDATE t_schedule SET display_option_type = :display_option_type WHERE schedule_id = :schedule_id ;";
        return $this->_update($sql, ['display_option_type' => $display_option_type, 'schedule_id' => $schedule_id]);
    }

    public function updateActFlag($call_id)
    {
        $sql = "UPDATE t_call SET act_flag = :act_flag WHERE call_id = :call_id;";
        return $this->_update($sql, ['call_id' => $call_id, 'act_flag' => 0]);
    }

    public function getCall($call_id)
    {
        $sql = "SELECT visit_id,plan_flag,act_flag FROM t_call WHERE call_id = :call_id;";
        return $this->_first($sql, ['call_id' => $call_id]);
    }

    public function getVisit($schedule_id,$visit_id)
    {
        $sql = "SELECT important_flag FROM t_visit WHERE schedule_id = :schedule_id AND visit_id = :visit_id;";
        return $this->_first($sql, ['schedule_id' => $schedule_id, 'visit_id' => $visit_id]);
    }

    public function getSchedule($schedule_id)
    {
        $sql = "
        SELECT 
            schedule_id,
            schedule_type,
            start_date,
            start_time,
            end_time,
            title,
            all_day_flag,
            display_option_type,
            user_cd 
        FROM t_schedule 
        WHERE schedule_id = :schedule_id;";
        return $this->_first($sql, ['schedule_id' => $schedule_id]);
    }

    public function getInfoPerson($person_cd)
    {
        $sql = "
            SELECT 
                m_person.person_name,
                m_facility_person.department_cd,
                m_department.department_name,
                m_position.position_name
            FROM m_person
            LEFT JOIN m_facility_person
                ON m_person.person_cd = m_facility_person.person_cd
            LEFT JOIN m_department
                ON m_facility_person.department_cd = m_department.department_cd
            LEFT JOIN m_position
                ON m_position.position_cd = m_facility_person.display_position_cd
            WHERE m_person.person_cd = :person_cd ;";

        return $this->_first($sql, ['person_cd' => $person_cd]);
    }

    public function updateCall($call_id, $off_label_flag, $off_label_concent_flag, $off_label_call_time, $related_product, $question, $answer, $re_question, $literature, $act_flag, $person_info, $person_name)
    {
        $sql = "
                UPDATE t_call
                    SET off_label_flag = :off_label_flag,
                        off_label_concent_flag = :off_label_concent_flag,
                        off_label_call_time = :off_label_call_time,
                        related_product = :related_product,
                        question = :question,
                        answer = :answer,
                        re_question = :re_question,
                        literature = :literature,
                        person_name = :person_name,
                        department_cd = :department_cd,
                        department_name = :department_name,
                        display_position_name = :display_position_name,
                        act_flag = :act_flag
                WHERE call_id = :call_id; ";

        return $this->_update($sql, [
            'call_id' => $call_id,
            'off_label_flag' => $off_label_flag == 1 ? $off_label_flag : 0,
            'off_label_concent_flag' => $off_label_flag == 1 ? $off_label_concent_flag : 0,
            'off_label_call_time' => $off_label_flag == 1 ? ($off_label_call_time ?? null ) : null,
            'related_product' => $off_label_flag == 1 ? $related_product : null,
            'question' => $off_label_flag == 1 ? $question : null,
            'answer' => $off_label_flag == 1 ? $answer : null,
            're_question' => $off_label_flag == 1 ? $re_question : null,
            'literature' => $off_label_flag == 1 ? $literature : null,
            'person_name' => $person_info->person_name ?? $person_name,
            'department_cd' => $person_info->department_cd ?? '',
            'department_name' => $person_info->department_name ?? '',
            'display_position_name' => $person_info->position_name ?? '',
            'act_flag' => $act_flag
        ]);
    }

    public function deleteDetail($detail_id,$facility_cd, $person_cd,$product_cd,$start_date)
    {
        // delete t_detail ,t_detail_document , t_document_usage_situation
        $sql = "
            DELETE t_detail,t_detail_document,t_document_usage_situation
            FROM t_detail
            LEFT JOIN t_detail_document
                ON t_detail.detail_id = t_detail_document.detail_id
            LEFT JOIN t_document_usage_situation
                ON t_document_usage_situation.document_id = t_detail_document.document_id
            WHERE t_detail.detail_id = :detail_id;";

        $t_detail = $this->_destroy($sql, ['detail_id' => $detail_id]);

        // delete t_reaction_cd
        $sql = "DELETE FROM t_reaction_cd WHERE facility_cd = :facility_cd AND person_cd = :person_cd AND product_cd = :product_cd AND final_content_date = :final_content_date;";
        $t_reaction_cd = $this->_destroy($sql,['facility_cd' => $facility_cd , 'person_cd' => $person_cd , 'product_cd' => $product_cd , 'final_content_date' => $start_date]);

        // delete t_phase
        $sql = "DELETE FROM t_phase WHERE facility_cd = :facility_cd AND person_cd = :person_cd AND product_cd = :product_cd AND final_content_date = :final_content_date;";
        $t_phase = $this->_destroy($sql,['facility_cd' => $facility_cd , 'person_cd' => $person_cd , 'product_cd' => $product_cd , 'final_content_date' => $start_date]);

        // delete t_prescription
        $sql = "DELETE FROM t_prescription WHERE facility_cd = :facility_cd AND person_cd = :person_cd AND product_cd = :product_cd AND final_content_date = :final_content_date;";
        $t_prescription = $this->_destroy($sql,['facility_cd' => $facility_cd , 'person_cd' => $person_cd , 'product_cd' => $product_cd , 'final_content_date' => $start_date]);

        return $t_detail && $t_reaction_cd && $t_phase && $t_prescription;
    }

    public function checkDetail($detail_id , $call_id)
    {
        $sql = "SELECT COUNT(*) AS sum FROM t_detail WHERE detail_id = :detail_id AND call_id = :call_id;";
        return $this->_first($sql, ['detail_id' => $detail_id, 'call_id' => $call_id]);
    }

    public function updateDetail($detail_id,$call_id,$detail_order,$content_cd,$content_name_tran,$product_cd,$product_name,$note,$remarks,$phase_cd,$phase,$reaction_cd,$reaction,$prescription_count)
    {
        $sql = "
                UPDATE t_detail
                    SET detail_order = :detail_order,
                    content_cd = :content_cd,
                    content_name_tran = :content_name_tran,
                    product_cd = :product_cd,
                    product_name = :product_name,
                    note = :note,
                    remarks = :remarks,
                    phase_cd = :phase_cd,
                    phase = :phase,
                    reaction_cd = :reaction_cd,
                    reaction = :reaction,
                    prescription_count = :prescription_count
                WHERE detail_id = :detail_id
                AND call_id = :call_id;";

        return $this->_update($sql, [
            'detail_id' => $detail_id,
            'call_id' => $call_id,
            'detail_order' => $detail_order,
            'content_cd' => $content_cd,
            'content_name_tran' => $content_name_tran,
            'product_cd' => $product_cd,
            'product_name' => $product_name,
            'note' => $note,
            'remarks' => $remarks,
            'phase_cd' => $phase_cd,
            'phase' => $phase,
            'reaction_cd' => $reaction_cd,
            'reaction' => $reaction,
            'prescription_count' => $prescription_count
        ]);
    }

    public function insertDetail($call_id,$detail_order,$content_cd,$content_name_tran,$product_cd,$product_name,$note,$remarks,$phase_cd,$phase,$reaction_cd,$reaction,$prescription_count)
    {
        $sql = "
            INSERT INTO t_detail 
                (call_id,detail_order,content_cd,content_name_tran,product_cd,product_name,note,remarks,phase_cd,phase,reaction_cd,reaction,prescription_count) 
            VALUES 
                (:call_id,:detail_order,:content_cd,:content_name_tran,:product_cd,:product_name,:note,:remarks,:phase_cd,:phase,:reaction_cd,:reaction,:prescription_count);";
            $this->_create($sql, [
                'call_id' => $call_id,
                'detail_order' => $detail_order,
                'content_cd' => $content_cd,
                'content_name_tran' => $content_name_tran,
                'product_cd' => $product_cd,
                'product_name' => $product_name,
                'note' => $note,
                'remarks' => $remarks,
                'phase_cd' => $phase_cd,
                'phase' => $phase,
                'reaction_cd' => $reaction_cd,
                'reaction' => $reaction,
                'prescription_count' => $prescription_count
            ]);

        return $this->_lastInserted('t_detail', 'detail_id');
    }

    public function deleteDetailDocument($detail_id)
    {
        $sql = "DELETE FROM t_detail_document WHERE detail_id = :detail_id;";
        return $this->_destroy($sql, ['detail_id' => $detail_id]);
    }

    public function insertDetailDocument($detail_id, $document_id)
    {
        $sql = "INSERT INTO t_detail_document 
                    (detail_id,document_id) 
                VALUES 
                    (:detail_id,:document_id);";

        return $this->_create($sql, [
            'detail_id' => $detail_id,
            'document_id' => $document_id
        ]);
    }

    public function checkReaction($facility_cd, $person_cd, $product_cd, $reaction_cd)
    {
        $sql = "SELECT facility_cd,final_content_date FROM t_reaction_cd WHERE facility_cd = :facility_cd AND person_cd = :person_cd AND product_cd = :product_cd;";
        return $this->_first($sql,['facility_cd' => $facility_cd, 'person_cd' => $person_cd, 'product_cd' => $product_cd]);
    }

    public function insertReaction($facility_cd, $person_cd, $start_date, $product_cd, $reaction_cd)
    {
        $sql = "INSERT INTO t_reaction_cd (facility_cd,person_cd,product_cd,reaction_cd,final_content_date) VALUES (:facility_cd,:person_cd,:product_cd,:reaction_cd,:final_content_date);";
        return $this->_create($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd, 'product_cd' => $product_cd, 'reaction_cd' => $reaction_cd, 'final_content_date' => $start_date]);
    }

    public function updateReaction($facility_cd, $person_cd, $start_date, $product_cd, $reaction_cd)
    {
        $sql = "UPDATE t_reaction_cd
                    SET 
                        reaction_cd = :reaction_cd,
                        final_content_date = :final_content_date
                WHERE facility_cd = :facility_cd
                AND person_cd = :person_cd
                AND product_cd = :product_cd;";
        
        return $this->_update($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd, 'product_cd' => $product_cd, 'reaction_cd' => $reaction_cd, 'final_content_date' => $start_date]);
    }

    public function checkPhase($facility_cd, $person_cd, $product_cd)
    {
        $sql = "SELECT facility_cd,final_content_date FROM t_phase WHERE facility_cd = :facility_cd AND person_cd = :person_cd AND product_cd = :product_cd;";
        return $this->_first($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd, 'product_cd' => $product_cd]);
    }

    public function insertPhase($facility_cd, $person_cd, $start_date, $phase_cd, $product_cd)
    {
        $sql = "INSERT INTO t_phase (facility_cd,person_cd,product_cd,phase_cd,final_content_date) VALUES (:facility_cd,:person_cd,:product_cd,:phase_cd,:final_content_date);";
        return $this->_create($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd, 'product_cd' => $product_cd, 'phase_cd' => $phase_cd, 'final_content_date' => $start_date]);
    }

    public function updatePhase($facility_cd, $person_cd, $start_date, $phase_cd, $product_cd)
    {
        $sql = "
            UPDATE  t_phase
                SET 
                    phase_cd = :phase_cd,
                    final_content_date = :final_content_date
            WHERE facility_cd = :facility_cd
            AND person_cd = :person_cd
            AND product_cd = :product_cd;
            ";
        return $this->_update($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd, 'product_cd' => $product_cd, 'phase_cd' => $phase_cd, 'final_content_date' => $start_date]);
    }
    
    public function checkPrescription($facility_cd, $person_cd, $product_cd)
    {
        $sql = "SELECT facility_cd,final_content_date FROM t_prescription WHERE facility_cd = :facility_cd AND person_cd = :person_cd  AND product_cd = :product_cd;";
        return $this->_first($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd, 'product_cd' => $product_cd]);
    }

    public function insertPrescription($facility_cd, $person_cd, $start_date, $prescription_count, $product_cd)
    {
        $sql = "INSERT INTO t_prescription (facility_cd,person_cd,product_cd,prescription_count,final_content_date) VALUES (:facility_cd,:person_cd,:product_cd,:prescription_count,:final_content_date);";
        return $this->_create($sql, ['facility_cd' => $facility_cd, 'person_cd' => $person_cd, 'product_cd' => $product_cd, 'prescription_count' => $prescription_count, 'final_content_date' => $start_date]);
    }

    public function updatePrescription($facility_cd, $person_cd, $start_date, $prescription_count, $product_cd)
    {
        $sql = "UPDATE  t_prescription
                            SET 
                                prescription_count = :prescription_count,
                                final_content_date = :final_content_date
                        WHERE facility_cd = :facility_cd
                        AND person_cd = :person_cd
                        AND product_cd = :product_cd;";
        return $this->_update($sql, [
            'facility_cd' => $facility_cd, 'person_cd' => $person_cd, 'product_cd' => $product_cd, 'prescription_count' => $prescription_count, 'final_content_date' => $start_date
        ]);
    }

    public function getMasterVariable()
    {
        $sql = "SELECT definition_value FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label;";
        return $this->_first($sql, [
            'definition_name' => '資材使用機能区分',
            'definition_label' => '面談'
        ]);
    }

    public function getDocumentUsageSituation($document_id,$document_usage_id)
    {
        $sql = "SELECT document_id FROM t_document_usage_situation WHERE document_usage_id = :document_usage_id and document_id = :document_id;";
        return $this->_find($sql,[ "document_usage_id" => $document_usage_id , "document_id" => $document_id ]);
    }

    public function deleteDocumentUsageSituation($document_id,$call_id)
    {
        $sql = "
        DELETE t_document_usage_situation,t_document_review
        FROM t_document_usage_situation 
        LEFT JOIN t_document_review 
        ON (t_document_review.usage_situation_id = t_document_usage_situation.usage_situation_id AND t_document_usage_situation.document_id = t_document_review.document_id)
        WHERE t_document_usage_situation.document_id IN " . $this->_buildCommaString($document_id,true) ." 
        AND t_document_usage_situation.document_usage_id = :document_usage_id;";
        return $this->_destroy($sql, ['document_usage_id' => $call_id]);
    }

    public function insertDocumentUsageSituation($document_id,$definition_value,$call_id,$usage_org_label,$usage_user_cd,$usage_user_name,$start_time)
    {
        $sql = "
            INSERT INTO t_document_usage_situation
                (document_id,document_usage_type,document_usage_id,usage_org_label,usage_user_cd,usage_user_name,usage_datetime)
            VALUES
                (:document_id,:document_usage_type,:document_usage_id,:usage_org_label,:usage_user_cd,:usage_user_name,:usage_datetime);
        ";

        return $this->_create($sql, [
            'document_id' => $document_id,
            'document_usage_type' => $definition_value,
            'document_usage_id' => $call_id,
            'usage_org_label' => $usage_org_label,
            'usage_user_cd' => $usage_user_cd,
            'usage_user_name' => $usage_user_name,
            'usage_datetime' => $start_time
        ]);
    }
}
