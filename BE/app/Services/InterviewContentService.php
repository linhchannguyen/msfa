<?php

namespace App\Services;

use App\Repositories\InterviewContent\InterviewContentRepositoryInterface;

class InterviewContentService
{
    private $repo;

    public function __construct(InterviewContentRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getData()
    {
        return $this->repo->getData();
    }
}
