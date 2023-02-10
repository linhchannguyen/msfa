<?php

namespace App\Repositories\PostedUserManagement;

use App\Repositories\BaseRepository;

class PostedUserManagementRepository extends BaseRepository implements PostedUserManagementRepositoryInterface
{
    protected $useAutoMeta = true;

    public function allPostingProhibited()
    {
        $sql = "SELECT
        t_posting_prohibited.user_cd,
        m_org.org_label,
        m_user.user_name,
        t_file.file_url as avatar_image_data,
        t_file.mime_type as avatar_image_type,
        t_posting_prohibited.prohibited_datetime
        FROM t_posting_prohibited
        JOIN m_user ON m_user.user_cd = t_posting_prohibited.user_cd
        LEFT JOIN t_user_profile
            ON m_user.user_cd = t_user_profile.user_cd
        LEFT JOIN t_file
            ON t_file.file_id = t_user_profile.profile_image_file_id
        LEFT JOIN m_user_org ON m_user_org.user_cd = m_user.user_cd AND m_user_org.main_flag = 1
        LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
        ORDER BY prohibited_datetime DESC";
        return $this->_find($sql, []);
    }

    /*
    * description: key_type (10 質問, 20 回答)
    * created_by: chan_nl
    * updated_by: chan_nl
    */
    public function getUnsuitableReport($user_cd)
    {
        $sql = "SELECT
        t_unsuitable_report.key_type,
        t_unsuitable_report.key_id,
        (CASE WHEN t_question.question_id IS NOT NULL THEN t_question.created_at ELSE t_answer.created_at END) as created_at,
        (CASE WHEN t_question.question_id IS NOT NULL THEN t_question.question_id ELSE t_answer.question_id END) as question_id,
        t_question.contents,
        t_answer.answer_note
        FROM t_unsuitable_report
        LEFT JOIN t_question ON t_question.question_id = t_unsuitable_report.key_id AND t_unsuitable_report.key_type = 10
        LEFT JOIN t_answer ON t_answer.answer_id = t_unsuitable_report.key_id AND t_unsuitable_report.key_type = 20
        WHERE (t_question.create_user_cd = :question_user_cd OR t_answer.create_user_cd = :answer_user_cd)
            AND (t_question.question_id IS NOT NULL OR t_answer.question_id IS NOT NULL)
        GROUP BY t_unsuitable_report.key_type, t_unsuitable_report.key_id
        ORDER BY created_at DESC";
        return $this->_find($sql, ['question_user_cd' => $user_cd, 'answer_user_cd' => $user_cd]);
    }

    public function cancelPostingProhibited($user_cd)
    {
        $sql = "DELETE FROM t_posting_prohibited WHERE user_cd = :user_cd";
        return $this->_destroy($sql, ['user_cd' => $user_cd]);
    }
}