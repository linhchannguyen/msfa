<?php

namespace App\Repositories\GetSystemParameter;

use App\Repositories\BaseRepository;
use App\Repositories\GetSystemParameter\GetSystemParameterRepositoryInterface;

class GetSystemParameterRepository extends BaseRepository implements GetSystemParameterRepositoryInterface
{
    protected $useAutoMeta = true;

    public function getSystemParameter()
    {
        $sql = "
            SELECT 
                parameter_name,
                parameter_key,
                parameter_value,
                remarks
            FROM c_system_parameter
            WHERE parameter_key = :parameter_key;
        ";
        return $this->_first($sql, [
            'parameter_key' => 'システム名'
        ]);
    }

    public function getAllDataFromSystemParameterByName($parameterName)
    {
        $sql = "
            SELECT 
                parameter_name,
                parameter_key,
                parameter_value,
                remarks
            FROM c_system_parameter
            WHERE parameter_name = :parameter_name;
        ";
        return $this->_first($sql, [
            'parameter_name' => $parameterName
        ]);
    }
}
