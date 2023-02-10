<?php

namespace App\Repositories\Develop;

use App\Repositories\BaseRepository;
use App\Repositories\Develop\DevelopRepositoryInterface;

class DevelopRepository extends BaseRepository implements DevelopRepositoryInterface
{
    public function login($develop_cd)
    {
        $sql = "
        SELECT
            m_developer.developer_cd as user_cd,
            m_developer.developer_name,
            m_developer.password_hash as password
        FROM
            m_developer
        WHERE
            m_developer.developer_cd = :develop_cd 
        LIMIT 1;";

        return $this->_find($sql, ['develop_cd' => $develop_cd]);
    }
}
