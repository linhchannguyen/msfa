<?php

namespace App\Repositories\Item;

use App\Repositories\BaseRepositoryInterface;

interface ItemRepositoryInterface extends BaseRepositoryInterface
{
    public function allVariable();
    public function allProductGroup();
    public function allProductSelectFilter($data);
}
