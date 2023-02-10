<?php

namespace App\Repositories\Stock;

use App\Repositories\BaseRepositoryInterface;

interface StockRepositoryInterface extends BaseRepositoryInterface
{
    public function search($conditions, $limit, $offset);

    public function edit($conditions);

    public function create($data);

    public function createStockProduct($data);

    public function _lastInsertedStock();

    public function delete($conditions);

    public function contents();

    public function facilityCategory();
}
