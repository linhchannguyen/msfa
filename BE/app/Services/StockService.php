<?php

namespace App\Services;

use App\Repositories\Stock\StockRepositoryInterface;


class StockService
{
    private $repo;

    public function __construct(StockRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function search($conditions, $limit, $offset)
    {
        return $this->repo->search($conditions, $limit, $offset);
    }

    public function edit($conditions)
    {
        return $this->repo->edit($conditions);
    }

    public function create($data)
    {
        foreach($data->stocks as $stock){
            $this->repo->create($stock);
            $lastStockID = $this->repo->_lastInsertedStock();
            $products = [];
            foreach ($data->product_cds as $product_cd) {
                array_push($products, [
                    'stock_id' => $lastStockID->stock_id,
                    'product_cd' => $product_cd
                ]);
                break;
            }
            if(!empty($products)){
                $this->repo->createStockProduct($products);
            }
        }
    }

    public function delete($conditions)
    {
        return $this->repo->delete($conditions);
    }

    public function contents()
    {
        return $this->repo->contents();
    }

    public function facilityCategory()
    {
        return $this->repo->facilityCategory();
    }
}
