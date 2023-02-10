<?php

namespace App\Repositories\InterviewContent;

use App\Repositories\BaseRepository;
use App\Repositories\InterviewContent\InterviewContentRepositoryInterface;

class InterviewContentRepository extends BaseRepository implements InterviewContentRepositoryInterface
{

    public function getData()
    {
        $sql = "SELECT A.content_cd, A.content_name
            FROM m_content A INNER JOIN m_facility_category_type_content B ON A.content_cd = B.content_cd
            GROUP BY B.content_cd;";
        return $this->_find($sql, []);
    }
}
