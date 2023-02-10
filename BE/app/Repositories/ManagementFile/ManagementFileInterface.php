<?php

namespace App\Repositories\ManagementFile;

use App\Repositories\BaseRepositoryInterface;

interface ManagementFileInterface extends BaseRepositoryInterface
{
    public function getUrlS3 ($type, $userCdLogin, $idConvention = null, $idDocument = null, $typeOrinal = null, $pageNum = null, $idFile = null);
}
