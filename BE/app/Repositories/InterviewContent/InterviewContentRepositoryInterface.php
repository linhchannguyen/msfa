<?php

namespace App\Repositories\InterviewContent;

use App\Repositories\BaseRepositoryInterface;

interface InterviewContentRepositoryInterface extends BaseRepositoryInterface
{
    public function getData();
}
