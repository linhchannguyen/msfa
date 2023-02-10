<?php

namespace App\Services;

use App\Repositories\Item\ItemRepositoryInterface;

class ItemService
{
    private $repo;

    public function __construct(ItemRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function GetListItem()
    {
        $result['variable'] = $this->repo->allVariable();
        $product = $this->repo->allProductGroup();
        array_unshift($product, array("product_group_cd" => "", "product_group_name" => "全て"));
        $result['product_group'] = $product;
        return $result;
    }

    public function getDataFilter($data)
    {
        $product = $this->repo->allProductSelectFilter($data);
        return $product;
    }
}
