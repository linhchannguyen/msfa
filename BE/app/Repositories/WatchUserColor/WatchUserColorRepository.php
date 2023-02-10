<?php

namespace App\Repositories\WatchUserColor;

use App\Repositories\BaseRepository;
use App\Repositories\WatchUserColor\WatchUserColorRepositoryInterface;
use App\Traits\DateTimeTrait;

class WatchUserColorRepository extends BaseRepository implements WatchUserColorRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = true;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function getVariableDefinition()
    {
        $sql = "SELECT definition_value, definition_label FROM m_variable_definition WHERE definition_name = :definition_name ORDER BY sort_order ASC";
        return $this->_find($sql, ['definition_name' => SCHEDULE_DIVISION]);
    }

    public function  getList()
    {
        $sql = "SELECT
                display_color_cd,
                display_color,
                memo,
                sort_order
            FROM
                m_watch_user_color
            ORDER BY
                sort_order ASC";
        return $this->_all($sql);
    }

    public function  getListByUser($startDate, $endDate, $user_cd, $type, $currentUser)
    {
        $sql = "SELECT
                t_schedule.schedule_id,
                t_schedule.schedule_type,
                t_schedule.start_date,
                t_schedule.start_time,
                t_schedule.end_time,
                t_schedule.title,
                t_schedule.all_day_flag,
                t_schedule.display_option_type,
                t_schedule.user_cd,
                t_watch_user.display_color_cd,
                m_watch_user_color.display_color
            FROM t_schedule
            LEFT JOIN t_watch_user ON t_schedule.user_cd = t_watch_user.watch_user_cd AND t_watch_user.user_cd = :user_cd
            LEFT JOIN m_watch_user_color ON t_watch_user.display_color_cd = m_watch_user_color.display_color_cd
            WHERE t_schedule.non_display_flag = 0 AND  CAST(t_schedule.start_date AS DATE) BETWEEN :start_date AND :end_date";
        if (!empty($user_cd)) {
            $sql .= " AND t_watch_user.watch_user_cd IN " . $this->_buildCommaString($user_cd, true);
        }
        if (!empty($type)) {
            $sql .= " AND t_schedule.schedule_type IN " . $this->_buildCommaString($type, true);
        }
        $sql .= " ORDER BY t_schedule.start_date ASC";
        return $this->_find($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'user_cd' => $currentUser
        ]);
    }

    public function getWatchUserList($currentUser)
    {
        $sql = "SELECT
                t_watch_user.watch_user_cd,
                t_watch_user.display_flag,
                t_watch_user.user_cd,
                m_user.user_name,
                m_watch_user_color.display_color_cd,
                m_watch_user_color.display_color,
                m_user_org.user_post_type
            FROM t_watch_user
            LEFT JOIN m_watch_user_color ON t_watch_user.display_color_cd = m_watch_user_color.display_color_cd
            JOIN m_user ON m_user.user_cd = t_watch_user.watch_user_cd
            LEFT JOIN m_user_org ON m_user_org.user_cd = m_user.user_cd AND m_user_org.main_flag = 1
            LEFT JOIN m_org ON m_org.org_cd = m_user_org.org_cd
            where t_watch_user.user_cd = :currentUser
            ORDER BY m_org.sort_order ASC, m_user_org.user_post_type DESC, t_watch_user.watch_user_cd ASC";
        return $this->_find($sql, [
            'currentUser' => $currentUser
        ]);
    }

    public function check($userCd, $currentUser)
    {
        $sql = "SELECT
            user_cd,
            watch_user_cd,
            display_flag,
            display_color_cd
        FROM
            t_watch_user
        WHERE
            user_cd = :current_user AND
            watch_user_cd = :user_cd";
        return $this->_first($sql, [
            'current_user' => $currentUser,
            'user_cd' => $userCd
        ]);
    }

    public function create($data)
    {
        $sql = "INSERT INTO t_watch_user(
            user_cd,
            watch_user_cd,
            display_flag,
            display_color_cd)
        VALUES :values";
        return $this->_bulkCreate($sql, $data);
    }
    public function update($userCd, $displayColorCd, $displayFlag, $currentUser)
    {
        $sql = "UPDATE t_watch_user SET display_flag = :display_flag, display_color_cd = :display_color_cd 
        WHERE user_cd = :user_cd AND watch_user_cd = :watch_user_cd";
        return $this->_update($sql, [
            'user_cd' => $currentUser . '',
            'watch_user_cd' => $userCd . '',
            'display_flag' => $displayFlag,
            'display_color_cd' => $displayColorCd
        ]);
    }

    public function deleteWatchUser($currentUser, $user_cd)
    {
        $sql = "DELETE FROM t_watch_user WHERE user_cd = :user_cd AND watch_user_cd = :watch_user_cd";
        return $this->_destroy($sql, ['user_cd' => $currentUser, 'watch_user_cd' => $user_cd]);
    }
}
