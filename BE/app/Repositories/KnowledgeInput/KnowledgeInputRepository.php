<?php

namespace App\Repositories\KnowledgeInput;

use App\Repositories\BaseRepository;
use App\Repositories\KnowledgeInput\KnowledgeInputRepositoryInterface;
use App\Traits\DateTimeTrait;

class KnowledgeInputRepository extends BaseRepository implements KnowledgeInputRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = true;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d');
        $this->approval_business = "承認業務区分";
    }

    public function getVariableDefinitionApprovalWorkType($variable_name)
    {
        $query['definition_name'] = $this->approval_business;
        $query['definition_label'] = $variable_name;
        $sql = "SELECT definition_value FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label";
        return $this->_first($sql, $query);
    }

    public function getStatus($definition_name)
    {
        $sql = "SELECT definition_label,definition_value FROM m_variable_definition WHERE definition_name = :definition_name  ORDER BY sort_order ASC;";
        return $this->_find($sql, ['definition_name' => $definition_name]);
    }

    public function getCategory()
    {
        $sql = "SELECT knowledge_category_cd,knowledge_category_name FROM m_knowledge_category ORDER BY sort_order ASC;";
        return $this->_find($sql, []);
    }

    public function getMedicalSubjects()
    {
        $sql = "SELECT medical_subjects_group_cd,medical_subjects_group_name FROM m_medical_subjects_group;";
        return $this->_find($sql, []);
    }

    public function getActivePoint()
    {
        $sql = "SELECT definition_label,definition_value FROM m_variable_definition WHERE definition_name = :definition_name ORDER BY sort_order ASC; ";
        return $this->_find($sql, ['definition_name' => '公開時活用度ポイントコード']);
    }

    public function getLayerNum()
    {
        $sql = "SELECT parameter_key,parameter_value FROM c_system_parameter WHERE parameter_name = :parameter_name AND parameter_key = :parameter_key;";
        return $this->_first($sql, ['parameter_name' => '承認階層', 'parameter_key' => 'ナレッジ']);
    }

    public function getStatusKnowledge($knowledge_id)
    {
        $sql = "
            SELECT 
                t_knowledge.knowledge_status_type,
                m_variable_definition.definition_label 
            FROM t_knowledge 
            INNER JOIN m_variable_definition 
                ON (m_variable_definition.definition_name = :definition_name AND m_variable_definition.definition_value = t_knowledge.knowledge_status_type)
            WHERE t_knowledge.knowledge_id = :knowledge_id
            ORDER BY t_knowledge.knowledge_status_type ASC;";

        return $this->_first($sql, ['definition_name' => 'ナレッジステータス', 'knowledge_id' => $knowledge_id]);
    }

    public function knowledgeTab1AndTab2($knowledge_id,$user_cd, $approval_layer_num,$request_type)
    {
        $sql = "
            SELECT 
                t_knowledge.knowledge_id,
                t_knowledge.anonymous_flag,
                t_knowledge.title,
                t_knowledge.knowledge_category_cd,
                t_knowledge.product_name,
                t_knowledge.facility_short_name,
                t_knowledge.facility_cd,
                t_knowledge.person_cd,
                t_knowledge.person_name,
                t_knowledge.medical_subjects_group_cd,
                m_knowledge_category.knowledge_category_name,
                t_knowledge.medical_subjects_group_name,
                t_knowledge.create_user_cd,
                t_knowledge.product_cd,
                (
                    SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT('user_cd', t_knowledge_collaborator.user_cd , 'user_name' , m_user.user_name , 'org_label' , m_org.org_label , 'org_cd', m_org.org_cd )),']' ) 
                    FROM t_knowledge_collaborator 
                    INNER JOIN m_user
                        ON m_user.user_cd = t_knowledge_collaborator.user_cd
                    LEFT JOIN m_user_org
                        ON (m_user_org.user_cd = t_knowledge_collaborator.user_cd AND m_user_org.main_flag = 1)
                    LEFT JOIN m_org
                       ON m_org.org_cd = m_user_org.org_cd
                    WHERE t_knowledge_collaborator.knowledge_id = t_knowledge.knowledge_id
                ) as users , 
                t_knowledge.contents,
                t_knowledge.submit_comment,
                (
                    SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT(
                        'layer_num', t_approval_request_detail.layer_num ,
                        'comment' , t_approval_request_detail.comment ,
                        'approval_user_cd' , t_approval_request_detail.approval_user_cd)),']' ) 
                    FROM t_approval_request 
                    INNER JOIN t_approval_request_detail
                        ON t_approval_request_detail.request_id = t_approval_request.request_id
                    INNER JOIN m_variable_definition
                        ON (m_variable_definition.definition_name = :definition_name AND m_variable_definition.definition_label = :definition_label AND t_approval_request.request_type = m_variable_definition.definition_value)
                    WHERE t_approval_request.request_target_id = t_knowledge.knowledge_id
                    ORDER BY t_approval_request_detail.layer_num ASC
                ) AS comment,
                (
                    SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT(
                        'layer_num', t_approval_request_detail.layer_num ,
                        'comment' , t_approval_request_detail.comment ,
                        'approval_user_cd' , t_approval_request_detail.approval_user_cd,
                        'approval_datetime' , t_approval_request_detail.approval_datetime
                    )),']' ) 
                    FROM t_approval_request 
                    INNER JOIN t_approval_request_detail
                        ON (t_approval_request_detail.request_id = t_approval_request.request_id AND t_approval_request_detail.layer_num < :layer_num)
                    INNER JOIN m_variable_definition
                        ON (m_variable_definition.definition_name = :definition_name_or AND m_variable_definition.definition_label = :definition_label_or AND t_approval_request.request_type = m_variable_definition.definition_value)
                    WHERE t_approval_request.request_target_id = t_knowledge.knowledge_id
                    ORDER BY t_approval_request_detail.layer_num ASC
                ) AS comment_orther_approval,
                (
                    SELECT CONCAT( '[', GROUP_CONCAT( JSON_OBJECT(
                        'layer_num', t_approval_request_detail.layer_num ,
                        'comment' , t_approval_request_detail.comment ,
                        'approval_user_cd' , t_approval_request_detail.approval_user_cd,
                        'approval_datetime' , t_approval_request_detail.approval_datetime
                    )),']' ) 
                    FROM t_approval_request 
                    INNER JOIN t_approval_request_detail
                        ON (t_approval_request_detail.request_id = t_approval_request.request_id AND t_approval_request_detail.layer_num = :approval_layer_num)
                    INNER JOIN m_variable_definition
                        ON (m_variable_definition.definition_name = :definition_name_la AND m_variable_definition.definition_label = :definition_label_la AND t_approval_request.request_type = m_variable_definition.definition_value)
                    WHERE t_approval_request.request_target_id = t_knowledge.knowledge_id
                    ORDER BY t_approval_request_detail.layer_num ASC
                ) AS comment_last_approval,
                (
                    SELECT 
                        COUNT(*)
                    FROM m_user_role
                    WHERE m_user_role.user_cd = :user_cd
                    AND m_user_role.role = :role
                ) AS check_knowledge_admin,
                t_knowledge.active_point_cd,
                t_knowledge.first_release_datetime,
                t_knowledge.release_datetime,
                t_knowledge.release_flag,
                t_knowledge.create_datetime,
                t_approval_request.active_layer_num
            FROM t_knowledge
            LEFT JOIN m_knowledge_category
                ON t_knowledge.knowledge_category_cd = m_knowledge_category.knowledge_category_cd
            LEFT JOIN t_approval_request
                ON (t_approval_request.request_target_id = t_knowledge.knowledge_id AND t_knowledge.create_user_cd = t_approval_request.request_user_cd AND t_approval_request.request_type = :request_type)
            WHERE t_knowledge.knowledge_id = :knowledge_id;";

        return $this->_first($sql, [
            'knowledge_id' => $knowledge_id,
            'definition_name' => '承認申請区分',
            'definition_name_or' => '承認申請区分',
            'definition_name_la' => '承認申請区分',
            'definition_label' => 'ナレッジ',
            'definition_label_or' => 'ナレッジ',
            'definition_label_la' => 'ナレッジ',
            'user_cd' => $user_cd,
            'role' => 'R60',
            'layer_num' => $approval_layer_num,
            'approval_layer_num' => $approval_layer_num,
            'request_type' => $request_type
        ]);
    }

    public function getLastApproverLayer($knowledge_id,$date_system,$approval_work_type,$approval_layer_num)
    {
        $sql = "
            SELECT 
                h_approval_user.approval_user_cd,
                h_approval_user.approval_layer_num
            FROM h_approval_user
            INNER JOIN t_knowledge
                ON (h_approval_user.request_user_cd = t_knowledge.create_user_cd AND t_knowledge.knowledge_id = :knowledge_id)
            WHERE CAST(h_approval_user.start_date AS DATE)  <= :date_system AND :date_system_temp <= CAST(h_approval_user.end_date AS DATE)
            AND h_approval_user.approval_work_type = :approval_work_type
            AND h_approval_user.approval_layer_num = :approval_layer_num
            GROUP BY h_approval_user.approval_user_cd,h_approval_user.approval_layer_num
            ORDER BY h_approval_user.approval_layer_num DESC;
        ";
        return $this->_find($sql,[
            'knowledge_id' => $knowledge_id,
            'date_system' => $date_system,
            'date_system_temp' => $date_system,
            'approval_work_type' => $approval_work_type,
            'approval_layer_num' => $approval_layer_num
        ]);
    }

    public function getUserApproval($approval_work_type, $request_user_cd)
    {
        $query['approval_work_type'] = $approval_work_type;
        $query['request_user_cd'] = $request_user_cd;
        $query['start_date'] = $this->date;
        $query['end_date'] = $this->date;
        $sql = "SELECT approval_user_cd,approval_layer_num
                FROM h_approval_user T1 
                WHERE approval_work_type = :approval_work_type 
                    AND request_user_cd = :request_user_cd
                    AND start_date <= :start_date 
                    AND end_date >= :end_date
                GROUP BY approval_user_cd, approval_layer_num";
        return $this->_find($sql, $query);
    }

    public function getOtherLastApproverLayer($knowledge_id,$date_system,$approval_work_type,$approval_layer_num)
    {
        $sql = "
            SELECT 
                h_approval_user.approval_user_cd ,
                h_approval_user.approval_layer_num
            FROM h_approval_user
            INNER JOIN t_knowledge
                ON (h_approval_user.request_user_cd = t_knowledge.create_user_cd AND t_knowledge.knowledge_id = :knowledge_id)
            WHERE CAST(h_approval_user.start_date AS DATE)  <= :date_system 
            AND :date_system_temp <= CAST(h_approval_user.end_date AS DATE)
            AND h_approval_user.approval_work_type = :approval_work_type
            AND h_approval_user.approval_layer_num < :approval_layer_num
            GROUP BY h_approval_user.approval_user_cd,h_approval_user.approval_layer_num
            ORDER BY h_approval_user.approval_layer_num ASC ;"; 
        
        return $this->_find($sql,[
            'knowledge_id' => $knowledge_id,
            'date_system' => $date_system,
            'date_system_temp' => $date_system,
            'approval_work_type' => $approval_work_type,
            'approval_layer_num' => $approval_layer_num
        ]);
    }

    public function knowledgeNotes()
    {
        $sql = "SELECT definition_value,definition_label FROM m_variable_definition where definition_name = :definition_name ORDER BY sort_order ASC ;";
        return $this->_find($sql, ['definition_name' => 'チャネル']);
    }

    public function knowledgeTimeLine($knowledge_id)
    {
        $sql = "
            SELECT
                DATE_FORMAT(t_timeline.start_datetime,'%Y/%m/%d') as start_datetime,
                CONCAT( '[', GROUP_CONCAT( JSON_OBJECT(
                        'timeline_id', t_knowledge_timeline.timeline_id , 
                        'channel_type' , t_timeline.channel_type  
                    )),']' 
                ) as timeline_details
            FROM t_knowledge 
            INNER JOIN t_knowledge_timeline 
                ON  t_knowledge_timeline.knowledge_id = t_knowledge.knowledge_id
            INNER JOIN m_variable_definition
                ON (m_variable_definition.definition_value = t_knowledge_timeline.channel_type AND m_variable_definition.definition_name = :definition_name)
            INNER JOIN t_timeline
                ON t_timeline.timeline_id = t_knowledge_timeline.timeline_id
            WHERE t_knowledge.knowledge_id = :knowledge_id
            GROUP BY DATE_FORMAT(t_timeline.start_datetime,'%Y/%m/%d') 
            ORDER BY t_timeline.start_datetime DESC ,m_variable_definition.sort_order ASC;
        ";
        return $this->_find($sql, ['knowledge_id' => $knowledge_id, 'definition_name' => 'チャネル']);
    }

    public function knowledgeTimeLine10($timeline_id, $channel_type,$knowledge_id)
    {
        $sql = "
        SELECT 
            DATE_FORMAT(t_timeline.start_datetime,'%Y/%m/%d') as start_date,
            t_timeline.start_datetime,
            t_timeline.end_datetime,
            t_timeline.channel_type,
            t_timeline.channel_id,
            t_timeline.channel_id as call_id,
            (SELECT t_visit.schedule_id FROM t_call INNER JOIN t_visit ON t_visit.visit_id = t_call.visit_id WHERE t_call.call_id = t_timeline.channel_id ) AS schedule_id,
            GROUP_CONCAT(t_timeline.timeline_id) as timeline_id,
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
            t_knowledge_timeline.comment,
            0 AS delete_flag
        FROM t_timeline
        INNER JOIN t_knowledge_timeline
            ON (t_knowledge_timeline.timeline_id = t_timeline.timeline_id  AND t_knowledge_timeline.knowledge_id = :knowledge_id)
        WHERE t_timeline.timeline_id IN " . $this->_buildCommaString($timeline_id, true) . "
        AND t_timeline.channel_type = :channel_type
        GROUP BY t_timeline.channel_id
        ORDER BY t_timeline.detail_order;";

        return $this->_find($sql, ['channel_type' => $channel_type , 'knowledge_id' => $knowledge_id]);
        // AND t_knowledge_timeline.channel_type = t_timeline.channel_type
    }

    public function knowledgeTimeLine20($timeline_id, $channel_type,$knowledge_id)
    {
        $sql = "
            SELECT 
                DATE_FORMAT(t_timeline.start_datetime,'%Y/%m/%d') as start_date,
                t_timeline.start_datetime,
                t_timeline.end_datetime,
                t_timeline.channel_type,
                t_timeline.timeline_id,
                t_timeline.channel_id,
                t_timeline.off_label_flag,
                t_timeline.detail_order,
                t_timeline.content_name,
                t_timeline.phase_name,
                t_timeline.reaction_name,
                t_timeline.note,
                t_timeline.channel_detail_id,
                t_timeline.org_label,
                t_timeline.user_cd,
                t_timeline.user_name,
                t_timeline.user_post_type,
                t_timeline.title,
                t_timeline.convention_type_name,
                t_timeline.product_name,
                t_timeline.briefing_type_name,
                t_timeline.briefing_facility_short_name,
                t_timeline.facility_cd,
                t_timeline.facility_short_name,
                t_timeline.department_name,
                t_timeline.person_cd,
                t_timeline.person_name,
                t_timeline.content,
                t_knowledge_timeline.comment,
                0 AS delete_flag
            FROM t_timeline
            INNER JOIN t_knowledge_timeline
                ON (t_knowledge_timeline.timeline_id = t_timeline.timeline_id  AND t_knowledge_timeline.knowledge_id = :knowledge_id)
            WHERE t_timeline.timeline_id = :timeline_id
            AND t_timeline.channel_type = :channel_type
            GROUP BY t_timeline.channel_id;
        ";

        return $this->_first($sql, ['timeline_id' => $timeline_id, 'channel_type' => $channel_type , 'knowledge_id' => $knowledge_id]);
        // AND t_knowledge_timeline.channel_type = t_timeline.channel_type
    }

    public function knowledgeTimeLine30($timeline_id, $channel_type,$knowledge_id)
    {
        $sql = "
            SELECT 
                DATE_FORMAT(t_timeline.start_datetime,'%Y/%m/%d') as start_date,
                t_timeline.start_datetime,
                t_timeline.end_datetime,
                t_timeline.channel_type,
                t_timeline.timeline_id,
                t_timeline.channel_id,
                t_timeline.off_label_flag,
                t_timeline.detail_order,
                t_timeline.content_name,
                t_timeline.phase_name,
                t_timeline.reaction_name,
                t_timeline.note,
                t_timeline.channel_detail_id,
                t_timeline.org_label,
                t_timeline.user_cd,
                t_timeline.user_name,
                t_timeline.user_post_type,
                t_timeline.title,
                t_timeline.convention_type_name,
                t_timeline.product_name,
                t_timeline.briefing_type_name,
                t_timeline.briefing_facility_short_name,
                t_timeline.facility_cd,
                t_timeline.facility_short_name,
                t_timeline.department_name,
                t_timeline.person_cd,
                t_timeline.person_name,
                t_timeline.content,
                t_knowledge_timeline.comment,
                0 AS delete_flag
            FROM t_timeline
            INNER JOIN t_knowledge_timeline
                ON (t_knowledge_timeline.timeline_id = t_timeline.timeline_id  AND t_knowledge_timeline.knowledge_id = :knowledge_id)
            WHERE t_timeline.timeline_id = :timeline_id
            AND t_timeline.channel_type = :channel_type
            GROUP BY t_timeline.channel_id;
        ";

        return $this->_first($sql, ['timeline_id' => $timeline_id, 'channel_type' => $channel_type , 'knowledge_id' => $knowledge_id]);
        // AND t_knowledge_timeline.channel_type = t_timeline.channel_type
    }

    public function knowledgeTimeLineNone($timeline_id, $channel_type,$knowledge_id)
    {
        $sql = "
            SELECT 
                DATE_FORMAT(t_timeline.start_datetime,'%Y/%m/%d') as start_date,
                t_timeline.start_datetime,
                t_timeline.end_datetime,
                t_timeline.channel_type,
                t_timeline.timeline_id,
                t_timeline.channel_id,
                t_timeline.off_label_flag,
                t_timeline.detail_order,
                t_timeline.content_name,
                t_timeline.phase_name,
                t_timeline.reaction_name,
                t_timeline.note,
                t_timeline.channel_detail_id,
                t_timeline.org_label,
                t_timeline.user_cd,
                t_timeline.user_name,
                t_timeline.user_post_type,
                t_timeline.title,
                t_timeline.convention_type_name,
                t_timeline.product_name,
                t_timeline.briefing_type_name,
                t_timeline.briefing_facility_short_name,
                t_timeline.facility_cd,
                t_timeline.facility_short_name,
                t_timeline.department_name,
                t_timeline.person_cd,
                t_timeline.person_name,
                t_timeline.content,
                t_knowledge_timeline.comment,
                0 AS delete_flag
            FROM t_timeline
            INNER JOIN t_knowledge_timeline
                ON (t_knowledge_timeline.timeline_id = t_timeline.timeline_id  AND t_knowledge_timeline.knowledge_id = :knowledge_id)
            WHERE t_timeline.timeline_id = :timeline_id
            AND t_timeline.channel_type = :channel_type;";
        // AND t_knowledge_timeline.channel_type = t_timeline.channel_type

        return $this->_first($sql, ['timeline_id' => $timeline_id, 'channel_type' => $channel_type , 'knowledge_id' => $knowledge_id]);
    }

    public function getKnowledge($knowledge_id)
    {
        $sql = "SELECT knowledge_id,active_point_cd,first_release_datetime,last_update_datetime FROM t_knowledge WHERE knowledge_id = :knowledge_id ;";
        return $this->_first($sql,[ 'knowledge_id' => $knowledge_id ]);
    }

    public function getVariableDefinition($definition_name,$definition_label)
    {
        $sql = "SELECT definition_name,definition_value,definition_label FROM m_variable_definition WHERE definition_name = :definition_name AND definition_label = :definition_label ;";
        return $this->_first($sql, [ 'definition_name' => $definition_name , 'definition_label' => $definition_label ]);
    }

    public function getStatusApproval($definition_name)
    {
        $sql = "SELECT definition_value,definition_label FROM m_variable_definition where definition_name = :definition_name ORDER BY sort_order ASC;";
        return $this->_find($sql, [ 'definition_name' => $definition_name ]);
    }

    public function getFacilityGroup($facility_cd)
    {
        $sql = "
            SELECT 
                m_facility_type_group.facility_type_group_cd as facility_category_type,
                m_facility_type_group.facility_type_group_name as facility_category_name
            FROM m_facility 
            INNER JOIN m_facility_type 
                ON  m_facility_type.facility_type = m_facility.facility_type
            INNER JOIN m_facility_type_group 
                ON  m_facility_type_group.facility_type_group_cd = m_facility_type.facility_type_group
            WHERE m_facility.facility_cd = :facility_cd;
        ";
        return $this->_first($sql,[ 'facility_cd' => $facility_cd ]);
    }

    public function checkActivePoint($knowledge_id)
    {
        $sql = "
        SELECT 
            point_id,
            point_target_type,
            point_target_id,
            active_point,
            message,
            receive_user_cd
        FROM t_active_point 
        WHERE point_target_id = :point_target_id;";

        return $this->_find($sql,[ 'point_target_id' => $knowledge_id ]);
    }

    public function createActivePoint($knowledge_id,$active_point_cd,$point_target_type,$message,$create_user_cd)
    {
        $sql = "INSERT INTO t_active_point (point_target_id,active_point,point_target_type,message,receive_user_cd) VALUES (:point_target_id,:active_point,:point_target_type,:message,:receive_user_cd);";

        return $this->_create($sql , [
            "point_target_id" => $knowledge_id,
            "active_point" => $active_point_cd,
            "point_target_type" => $point_target_type,
            "message" => $message,
            "receive_user_cd" => $create_user_cd
        ]);
    }

    public function updateKnowledgePublic($knowledge_id,$knowledge_status_type,$first_release_datetime,$release_datetime,$last_update_datetime)
    {
        $sql = "
            UPDATE t_knowledge
                SET knowledge_status_type = :knowledge_status_type,";
        if(!empty($first_release_datetime)){
            $sql .= " first_release_datetime = :first_release_datetime , ";
            $param['first_release_datetime'] = $first_release_datetime;
        }
        $sql .= "release_datetime = :release_datetime ,
                 release_flag = :release_flag,
                 last_update_datetime = :last_update_datetime
                ";

        $sql .= " WHERE knowledge_id = :knowledge_id;";

        $param['knowledge_status_type'] = $knowledge_status_type;
        $param['release_datetime'] = $release_datetime;
        $param['release_flag'] = 1;
        $param['last_update_datetime'] = $last_update_datetime;
        $param['knowledge_id'] = $knowledge_id;

        return $this->_update($sql,$param);
    }

    public function updateKnowledgeInput($knowledge_id,$request)
    {
        $query = [];
        $sql = "
            UPDATE t_knowledge
                SET knowledge_status_type = :knowledge_status_type,
                anonymous_flag = :anonymous_flag,
                title = :title,
                knowledge_category_cd = :knowledge_category_cd,
                product_cd = :product_cd,
                product_name = :product_name,
                facility_cd = :facility_cd,
                facility_short_name = :facility_short_name,
                person_cd = :person_cd,
                person_name = :person_name,
                medical_subjects_group_cd = :medical_subjects_group_cd,
                medical_subjects_group_name = :medical_subjects_group_name,
                contents = :contents,";
                if($request->mode_screen == "edit" || $request->mode_screen == "create"){
                    $sql.= " submit_comment = :submit_comment, ";
                    $query["submit_comment"] = $request->submit_comment;
                }
                if($request->mode_screen == "admin"){
                    $sql .= " active_point_cd = :active_point_cd,";
                    $query["active_point_cd"] = $request->active_point_cd;
                }
                $sql .= "
                facility_type_group_cd = :facility_type_group_cd,
                facility_type_group_name = :facility_type_group_name,
                last_update_datetime = :last_update_datetime,
                department_cd = :department_cd,
                department_name = :department_name,
                display_position_cd = :display_position_cd,
                display_position_name = :display_position_name

            WHERE knowledge_id = :knowledge_id;";
            
        $query["knowledge_id"] = $knowledge_id;
        $query["knowledge_status_type"] = $request->knowledge_status_type;
        $query["anonymous_flag"] = $request->anonymous_flag;
        $query["title"] = $request->title;
        $query["knowledge_category_cd"] = $request->knowledge_category_cd;
        $query["product_cd"] = $request->product_cd;
        $query["product_name"] = $request->product_name;
        $query["facility_cd"] = $request->facility_cd;
        $query["facility_short_name"] = $request->facility_short_name;
        $query["person_cd"] = $request->person_cd;
        $query["person_name"] = $request->person_name;
        $query["medical_subjects_group_cd"] = $request->medical_subjects_group_cd;
        $query["medical_subjects_group_name"] = $request->medical_subjects_group_name;
        $query["contents"] = $request->contents;
        $query["facility_type_group_cd"] = $request->facility_type_group_cd;
        $query["facility_type_group_name"] = $request->facility_type_group_name;
        $query["last_update_datetime"] = $request->last_update_datetime;

        $query["department_cd"] = $request->department_cd;
        $query["department_name"] = $request->department_name;
        $query["display_position_cd"] = $request->display_position_cd;
        $query["display_position_name"] = $request->display_position_name;

        return $this->_update($sql,$query);
    }

    public function createKnowledgeInput($request)
    {
        $sql = "
            INSERT INTO t_knowledge
                (
                    knowledge_status_type,anonymous_flag,title,knowledge_category_cd,product_cd,product_name,facility_cd,facility_short_name,person_cd,person_name,
                    medical_subjects_group_cd,medical_subjects_group_name,contents,submit_comment,facility_type_group_cd,facility_type_group_name,create_datetime,last_update_datetime,create_user_cd,
                    department_cd,department_name,display_position_cd,display_position_name
                )
            VALUES
                (
                    :knowledge_status_type,:anonymous_flag,:title,:knowledge_category_cd,:product_cd,:product_name,:facility_cd,:facility_short_name,:person_cd,:person_name,
                    :medical_subjects_group_cd,:medical_subjects_group_name,:contents,:submit_comment,:facility_type_group_cd,:facility_type_group_name,:create_datetime,:last_update_datetime,:create_user_cd,
                    :department_cd,:department_name,:display_position_cd,:display_position_name
                );
        ";

        $this->_create($sql,[
            "knowledge_status_type" => $request->knowledge_status_type,
            "anonymous_flag" => $request->anonymous_flag,
            "title" => $request->title,
            "knowledge_category_cd" => $request->knowledge_category_cd,
            "product_cd" => $request->product_cd,
            "facility_cd" => $request->facility_cd,
            "facility_short_name" => $request->facility_short_name,
            "person_cd" => $request->person_cd,
            "product_name" => $request->product_name,
            "person_name" => $request->person_name,
            "medical_subjects_group_cd" => $request->medical_subjects_group_cd,
            "medical_subjects_group_name" => $request->medical_subjects_group_name,
            "contents" => $request->contents ?? '',
            "submit_comment" => $request->submit_comment,
            "facility_type_group_cd" => $request->facility_type_group_cd,
            "facility_type_group_name" => $request->facility_type_group_name,
            "create_datetime" => $request->create_datetime,
            "last_update_datetime" => $request->last_update_datetime,
            "create_user_cd" => $request->create_user_cd,
            "department_cd" => $request->department_cd,
            "department_name" => $request->department_name,
            "display_position_cd" => $request->display_position_cd,
            "display_position_name" => $request->display_position_name,
        ]);

        return $this->_lastInserted('t_knowledge', 'knowledge_id');
    }

    public function getMasterFacilityPerson($facility_cd, $person_cd)
    {
        $sql = "
            SELECT 
                m_facility_person.department_cd,
                m_department.department_name,
                m_facility_person.display_position_cd,
                m_position.position_name AS display_position_name
            FROM m_facility_person
            LEFT JOIN m_position
                ON m_position.position_cd = m_facility_person.display_position_cd
            LEFT JOIN m_department
                ON m_department.department_cd = m_facility_person.department_cd
            WHERE m_facility_person.facility_cd = :facility_cd
            AND m_facility_person.person_cd = :person_cd;
        ";
        return $this->_first($sql,[
            "facility_cd" => $facility_cd,
            "person_cd" => $person_cd
        ]);
    }

    public function createKnowledgeCollaborator($datas)
    {
        $sql = "INSERT INTO t_knowledge_collaborator (knowledge_id,user_cd) VALUES :values;";
        return $this->_bulkCreate($sql, $datas);
    }

    public function deleteKnowledgeCollaborator($knowledge_id)
    {
        $sql = "DELETE FROM t_knowledge_collaborator WHERE knowledge_id = :knowledge_id ;";
        return $this->_destroy($sql,[ 'knowledge_id' => $knowledge_id ]);
    }

    public function nonePublic($knowledge_id,$knowledge_status_type)
    {
        $sql = "
            UPDATE t_knowledge
                SET knowledge_status_type = :knowledge_status_type
            WHERE knowledge_id = :knowledge_id;
        ";

        return $this->_update($sql,[
            'knowledge_id' => $knowledge_id,
            'knowledge_status_type' => $knowledge_status_type
        ]);
    }

    public function noneRejection($knowledge_id,$knowledge_status_type,$submit_comment)
    {
        $sql = "
            UPDATE t_knowledge
                SET knowledge_status_type = :knowledge_status_type,
                    submit_comment = :submit_comment
            WHERE knowledge_id = :knowledge_id;
        ";

        return $this->_update($sql,[
            'knowledge_id' => $knowledge_id,
            'knowledge_status_type' => $knowledge_status_type,
            'submit_comment' => $submit_comment
        ]);
    }

    public function getInformCategory($inform_category_name)
    {
        $sql = "SELECT inform_category_cd  FROM m_inform_category WHERE inform_category_name = :inform_category_name;";
        return $this->_first($sql,[ 'inform_category_name' => $inform_category_name ]);
    }

    public function deleteKnowledge($knowledge_id)
    {
        $sql = "DELETE t_knowledge, t_knowledge_nice, t_knowledge_request
            FROM t_knowledge
            LEFT JOIN t_knowledge_nice ON t_knowledge_nice.knowledge_id = t_knowledge.knowledge_id
            LEFT JOIN t_knowledge_request ON t_knowledge_request.knowledge_id = t_knowledge.knowledge_id
            WHERE t_knowledge.knowledge_id = :knowledge_id ;";
        return $this->_destroy($sql,[ 'knowledge_id' => $knowledge_id ]);
    }

    public function getApprovalRequest($knowledge_id,$request_type)
    {
        $param = [];
        $sql = "
        SELECT 
            request_id,
            request_type,
            request_target_id,
            request_user_cd,
            request_datetime,
            status_type
        FROM t_approval_request 
        WHERE request_target_id = :request_target_id ";
        if(!empty($request_type)){
            $sql .= " AND request_type = :request_type ";
            $param['request_type'] = $request_type;
        }
        $sql .= ";";
        $param['request_target_id'] = $knowledge_id;

        return $this->_first($sql, $param);
    }

    public function deleteApprovalRequest($knowledge_id,$request_type)
    {
        $sql = "DELETE T1, T2 FROM t_approval_request T1
            JOIN t_approval_request_detail T2 ON T2.request_id = T1.request_id
            WHERE T1.request_target_id = :knowledge_id AND T1.request_type = :request_type;";
        return $this->_destroy($sql, [ 'knowledge_id' => $knowledge_id , "request_type" => $request_type]);
    }

    public function deleteKnowledgeTimeline($knowledge_id)
    {
        $sql = " DELETE FROM t_knowledge_timeline WHERE knowledge_id = :knowledge_id ;";
        return $this->_destroy($sql, [ 'knowledge_id' => $knowledge_id ]);
    }

    public function getApprovalLayer($knowledge_id,$layer_num)
    {
        $param = [];
        $sql = "
            SELECT 
                t_approval_request_detail.request_id,
                t_approval_request_detail.layer_num,
                t_approval_request_detail.approval_user_cd,
                t_approval_request_detail.status_type,
                t_approval_request_detail.comment,
                m_user.user_name,
                t_approval_request_detail.approval_datetime,
                t_approval_request.active_layer_num
            FROM t_approval_request 
            INNER JOIN t_approval_request_detail
                 ON t_approval_request_detail.request_id = t_approval_request.request_id 
            INNER JOIN m_variable_definition
                 ON (m_variable_definition.definition_name = :definition_name AND m_variable_definition.definition_label = :definition_label 
                    AND t_approval_request.request_type = m_variable_definition.definition_value
                 )
            INNER JOIN m_user
                ON m_user.user_cd = t_approval_request_detail.approval_user_cd
            WHERE t_approval_request.request_target_id = :knowledge_id ";
            if(!empty($layer_num)){
                $sql .= " AND t_approval_request_detail.layer_num = :layer_num";
                $param['layer_num'] = $layer_num;
            }
            $sql .= " ORDER BY t_approval_request_detail.request_id , t_approval_request_detail.layer_num, t_approval_request_detail.approval_user_cd;";
        $param['knowledge_id'] = $knowledge_id;
        $param['definition_name'] = '承認申請区分';
        $param['definition_label'] = 'ナレッジ';

        return $this->_find($sql , $param);
    }

    public function updateCommentApprovalRequestDetail($request_id, $layer_num, $approval_user_cd,$comment)
    {
        $sql = "
            UPDATE t_approval_request_detail
                SET comment = :comment
            WHERE request_id = :request_id
            AND layer_num = :layer_num
            AND approval_user_cd = :approval_user_cd;
        ";
        return $this->_update($sql,[ 
            'request_id' => $request_id,
            'comment' => $comment,
            'layer_num' => $layer_num,
            'approval_user_cd' => $approval_user_cd
        ]);
    }

    public function updateApprovalRequestDetail($request_id, $layer_num, $approval_user_cd, $status_type, $approval_datetime)
    {
        $param = [];
        $sql = "
            UPDATE t_approval_request_detail
                SET status_type = :status_type,
                    approval_datetime = :approval_datetime
            WHERE request_id = :request_id";
            if(!empty($layer_num)){
                $sql .= " AND layer_num = :layer_num";
                $param['layer_num'] = $layer_num;
            }

            if(!empty($approval_user_cd)){
                $sql .= " AND approval_user_cd = :approval_user_cd";
                $param['approval_user_cd'] = $approval_user_cd;
            }
            
            $sql .=";";

        return $this->_update($sql, array_merge($param,[
            'request_id' => $request_id,
            'status_type' => $status_type,
            'approval_datetime' => $approval_datetime
        ]));
    }

    public function updateApprovalRequest($request_id,$status_type,$active_layer_num)
    {
        $sql = "
            UPDATE t_approval_request
                SET status_type = :status_type,
                    active_layer_num = :active_layer_num
            WHERE request_id = :request_id;
        ";

        return $this->_update($sql,[
            "status_type" => $status_type,
            "request_id" => $request_id,
            "active_layer_num" => $active_layer_num
        ]);
    }

    public function updateStatusKnowledge($knowledge_id, $knowledge_status_type)
    {
        $sql = "
            UPDATE t_knowledge
                SET knowledge_status_type = :knowledge_status_type
            WHERE knowledge_id = :knowledge_id;
        ";

        return $this->_update($sql, [
            'knowledge_id' => $knowledge_id,
            'knowledge_status_type' => $knowledge_status_type
        ]);
    }

    public function updateFirstReleaseDatetime($knowledge_id, $first_approval_datetime)
    {
        $sql = "
            UPDATE t_knowledge
                SET first_approval_datetime = :first_approval_datetime
            WHERE knowledge_id = :knowledge_id;
        ";

        return $this->_update($sql, [
            'knowledge_id' => $knowledge_id,
            'first_approval_datetime' => $first_approval_datetime
        ]);
    }

    public function updateKnowledgeSubmit($knowledge_id, $knowledge_status_type, $active_point_cd)
    {
        $sql = "
            UPDATE t_knowledge
                SET 
                    knowledge_status_type = :knowledge_status_type,
                    active_point_cd = :active_point_cd
            WHERE knowledge_id = :knowledge_id;
        ";
        return $this->_update($sql,[
            'knowledge_id' => $knowledge_id,
            'knowledge_status_type' => $knowledge_status_type,
            'active_point_cd' => $active_point_cd ?? null
        ]);
    }

    public function createApprovalRequest($request_target_id,$create_user_cd,$request_type,$status_type,$request_datetime)
    {
        $sql = "INSERT INTO t_approval_request (request_target_id,request_user_cd,request_type,status_type,request_datetime,active_layer_num) VALUES (:request_target_id,:request_user_cd,:request_type,:status_type,:request_datetime,:active_layer_num);";

        $this->_create($sql , [
            "request_target_id" => $request_target_id,
            "request_user_cd" => $create_user_cd,
            "request_type" => $request_type,
            "status_type" => $status_type,
            "request_datetime" => $request_datetime,
            "active_layer_num" => 1
        ]);

        return $this->_lastInserted('t_approval_request', 'request_id');
    }

    public function checkKnowledgeTimeline($knowledge_id, $timeline_id)
    {
        $sql = "SELECT knowledge_id,timeline_id,channel_type FROM t_knowledge_timeline WHERE knowledge_id = :knowledge_id AND timeline_id = :timeline_id;";
        return $this->_find($sql,[ 'knowledge_id' => $knowledge_id , 'timeline_id' => $timeline_id]);
    }

    public function updateKnowledgeTimeline($knowledge_id, $timeline_id,$comment)
    {
        $sql = "UPDATE t_knowledge_timeline SET comment = :comment WHERE knowledge_id = :knowledge_id AND timeline_id = :timeline_id;";
        return $this->_update($sql,[ 'comment' => $comment , 'knowledge_id' => $knowledge_id , 'timeline_id' => $timeline_id]);
    }

    public function insertKnowledgeTimeline($knowledge_id, $channel_type, $timeline_id, $channel_id, $comment)
    {
        $sql = "
            INSERT INTO t_knowledge_timeline
                (knowledge_id,channel_type,timeline_id,action_id,comment)
            VALUES
                (:knowledge_id,:channel_type,:timeline_id,:action_id,:comment);
        ";
        return $this->_create($sql,[
            'knowledge_id' => $knowledge_id,
            'channel_type' => $channel_type,
            'timeline_id' => $timeline_id,
            'action_id' => $channel_id,
            'comment' => $comment
        ]);
    }

    public function deleteKnowledgeTimelineDetails($knowledge_id, $timeline_id)
    {
        $sql = "DELETE FROM t_knowledge_timeline WHERE knowledge_id = :knowledge_id AND timeline_id = :timeline_id;";
        return $this->_destroy($sql,[ 'knowledge_id' => $knowledge_id , 'timeline_id' => $timeline_id]);
    }

    public function createApprovalRequestDetail($request_id,$layer_num,$approval_user_cd,$status_type,$approval_datetime)
    {
        $sql = "
            INSERT INTO t_approval_request_detail
                (request_id,layer_num,approval_user_cd,status_type,approval_datetime)
            VALUES
                (:request_id,:layer_num,:approval_user_cd,:status_type,:approval_datetime);
        ";
        return $this->_create($sql,[
            'request_id' => $request_id,
            'layer_num' => $layer_num,
            'approval_user_cd' => $approval_user_cd,
            'status_type' => $status_type,
            'approval_datetime' => $approval_datetime
        ]);
    }

    public function getActivePointTemp($remarks)
    {
        $sql = "SELECT fixed_active_point_cd,active_point,remarks FROM m_fixed_active_point WHERE remarks = :remarks;";
        return $this->_first($sql, ["remarks" => $remarks]);
    }

    public function checkRole($user_cd,$role)
    {
        $sql = "
            SELECT 
                user_cd,role
            FROM m_user_role
            WHERE m_user_role.user_cd = :user_cd
            AND m_user_role.role = :role;
        ";
        return $this->_find($sql,[
            'user_cd' => $user_cd,
            'role' => $role
        ]);
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
