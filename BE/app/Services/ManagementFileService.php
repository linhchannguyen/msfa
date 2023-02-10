<?php

namespace App\Services;

use App\Repositories\ManagementFile\ManagementFileInterface;

class ManagementFileService
{
    private $repo;

    public function __construct(ManagementFileInterface $repo)
    {
        $this->repo = $repo;
    }
    public function getFileUrlS3($type, $userCdLogin, $idConvention = null, $idDocument = null, $typeOrinal = null, $pageNum = null, $idFile = null)
    {
        return $this->repo->getUrlS3 ($type, $userCdLogin, $idConvention, $idDocument, $typeOrinal, $pageNum, $idFile);
    }
}