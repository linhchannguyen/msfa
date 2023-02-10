<?php

namespace App\Repositories\InformCategory;

use App\Repositories\BaseRepositoryInterface;

interface InformCategoryRepositoryInterface extends BaseRepositoryInterface
{
    //Search inform
    public function all();
}
