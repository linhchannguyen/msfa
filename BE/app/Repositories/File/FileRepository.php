<?php

namespace App\Repositories\File;

use App\Repositories\BaseRepository;
use App\Repositories\File\FileRepositoryInterface;

class FileRepository extends BaseRepository implements FileRepositoryInterface
{
    protected $useAutoMeta = true;

    public function save($data)
    {
        $sql = "
            INSERT INTO t_file(
                use_type,
                file_name,
                display_name,
                mime_type,
                file_url
            ) VALUES (
                :use_type,
                :file_name,
                :display_name,
                :mime_type,
                :file_url
            );";

        return $this->_create($sql, $data);
    }

    public function lastInserted()
    {
        return $this->_lastInserted('t_file', 'file_id');
    }
}
