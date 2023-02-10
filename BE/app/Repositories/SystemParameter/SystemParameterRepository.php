<?php

namespace App\Repositories\SystemParameter;

use App\Repositories\BaseRepository;

class SystemParameterRepository extends BaseRepository implements SystemParameterRepositoryInterface
{
    public function getTimezone()
    {
        $sql = "SELECT * FROM c_system_parameter WHERE parameter_key = :parameter_key";
        return $this->_first($sql, ['parameter_key' => config('variables.MSFA_SYSTEM_OPERATION_DATE')]);
    }
}
