<?php

namespace App\Services;

use App\Repositories\Inform\InformRepositoryInterface;

class InformService
{
    private $repo;

    public function __construct(InformRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function search($data, $limit, $offset)
    {
        return $this->repo->search($data, $limit, $offset);
    }

    public function archived($data)
    {
        return $this->repo->archived($data);
    }

    public function unarchived($data)
    {
        return $this->repo->unarchived($data);
    }

    public function archiveAll($user_cd)
    {
        return $this->repo->archiveAll($user_cd);
    }

    public function readInform($id)
    {
        return $this->repo->readInform($id);
    }

    public function installed($user_cd)
    {
        return $this->repo->installed($user_cd);
    }

    public function register($user_cd)
    {
        return $this->repo->register($user_cd);
    }

    public function delete($user_cd)
    {
        return $this->repo->delete($user_cd);
    }
}
