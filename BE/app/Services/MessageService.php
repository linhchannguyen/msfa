<?php

namespace App\Services;

use App\Repositories\Message\MessageRepositoryInterface;

class MessageService
{
    private $repo;

    public function __construct(MessageRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getMessageList()
    {
        return $this->repo->all();
    }

    public function getInfo($data)
    {
        return $this->repo->find($data);
    }

    public function create($data)
    {
        return $this->repo->create($data);
    }

    public function update($data, $id)
    {
        return $this->repo->update($data, $id);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
