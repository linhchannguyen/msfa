<?php

namespace App\Repositories\File;

use App\Repositories\BaseRepositoryInterface;

interface FileRepositoryInterface extends BaseRepositoryInterface
{
    public function save($data);
    public function lastInserted();
}
