<?php

namespace App\Repositories;

interface BaseRepositoryInterface {
    public function _all($sql);

    public function _find($sql, $data);
    
    public function _first($sql, $data);
    
    public function _create($sql, $data);

    public function _update($sql, $data);

    public function _destroy($sql, $data);

    public function _bulkCreate($sql, $data);

    public function _paginate($sql, $data, $paginateParams);

    public function _pluck(array $data, string $key);
}
