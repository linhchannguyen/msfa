<?php

namespace App\Repositories\Message;

use App\Repositories\BaseRepositoryInterface;

interface MessageRepositoryInterface extends BaseRepositoryInterface
{
    //Get message list
    public function all();

    //Get info
    public function find($id);

    //Create message
    public function create($data);

    //Update mesage
    public function update($data, $id);

    //Delete mesage
    public function delete($id);
}
