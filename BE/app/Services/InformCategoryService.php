<?php

namespace App\Services;

use App\Repositories\InformCategory\InformCategoryRepositoryInterface;

class InformCategoryService
{
    private $repo;

    public function __construct(InformCategoryRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function all()
    {
        return $this->repo->all();
    }
}
