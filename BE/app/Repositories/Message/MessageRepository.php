<?php

namespace App\Repositories\Message;

use App\Repositories\BaseRepository;
use App\Repositories\Message\MessageRepositoryInterface;
use App\Traits\DateTimeTrait;

class MessageRepository extends BaseRepository implements MessageRepositoryInterface
{
    use DateTimeTrait;
    protected $useAutoMeta = true;
    private $date;
    public function __construct()
    {
        $this->date = $this->currentJapanDateTime('Y-m-d H:i:s');
    }

    public function all()
    {
        $sql = "SELECT
            m.message_id,
            m.message_subject,
            m.release_start_date,
            m.release_end_date,
            m.release_org_cd,
            o.org_name,
            o.org_label,
            m.sender_name,
            m.message_contents,
            m.important_flag,
            m.last_update_datetime,
            m.create_user_cd 
        FROM
            t_message m
        LEFT JOIN m_org o ON m.release_org_cd = o.org_cd
        ORDER BY
            last_update_datetime DESC,
            release_end_date DESC";
        return $this->_find($sql, []);
    }

    public function find($id)
    {
        $sql = "SELECT
            m.message_id,
            m.message_subject,
            m.release_start_date,
            m.release_end_date,
            m.release_org_cd,
            o.org_name,
            o.org_label,
            m.sender_name,
            m.message_contents,
            m.important_flag,
            m.last_update_datetime,
            m.create_user_cd 
        FROM
            t_message m
        LEFT JOIN m_org o on m.release_org_cd = o.org_cd
        WHERE
            m.message_id = :message_id";
        return $this->_first($sql, ['message_id' => $id]);
    }

    public function create($data)
    {
        $sql = "INSERT 
            INTO t_message (
                important_flag, 
                message_subject, 
                release_start_date, 
                release_end_date, 
                release_org_cd,
                sender_name, 
                last_update_datetime, 
                message_contents, 
                create_user_cd) 
            VALUES (
                :important_flag,
                :message_subject, 
                :release_start_date, 
                :release_end_date, 
                :release_org_cd,
                :sender_name, 
                :last_update_datetime, 
                :message_contents, 
                :create_user_cd
            );";
        return $this->_create($sql, [
            'important_flag' => $data->important_flag,
            'message_subject' => $data->message_subject,
            'release_start_date' => $data->release_start_date,
            'release_end_date' => $data->release_end_date,
            'release_org_cd' => $data->release_org_cd ?? NULL,
            'sender_name' => $data->sender_name,
            'last_update_datetime' => $this->date,
            'message_contents' => $data->message_contents,
            'create_user_cd' => $data->user_cd
        ]);
    }

    public function update($data, $id)
    {
        $delete = "DELETE 
            FROM
                t_message_read 
            WHERE
                message_id = :message_id";

        $update = "UPDATE t_message
            SET important_flag = :important_flag,
                message_subject = :message_subject,
                release_start_date = :release_start_date,
                release_end_date = :release_end_date,
                release_org_cd = :release_org_cd,
                sender_name = :sender_name,
                last_update_datetime = :last_update_datetime,
                message_contents = :message_contents
            WHERE
                message_id = :message_id;";
        $result_1 = $this->_destroy($delete, ['message_id' => $id]);
        $result_2 = $this->_update($update, [
            'important_flag' => $data->important_flag,
            'message_subject' => $data->message_subject,
            'release_start_date' => $data->release_start_date,
            'release_end_date' => $data->release_end_date,
            'release_org_cd' => $data->release_org_cd ?? NULL,
            'sender_name' => $data->sender_name,
            'last_update_datetime' => $this->date,
            'message_contents' => $data->message_contents,
            'message_id' => $id
        ]);
        return $result_1 && $result_2;
    }

    public function delete($id)
    {
        $sql_delete_message_read = "DELETE FROM t_message_read where message_id = :message_id";
        $sql_delete_message = "DELETE FROM t_message where message_id = :message_id";
        $result_1 = $this->_destroy($sql_delete_message_read, ['message_id' => $id]);
        $result_2 = $this->_destroy($sql_delete_message, ['message_id' => $id]);
        return $result_1 && $result_2;
    }
}
