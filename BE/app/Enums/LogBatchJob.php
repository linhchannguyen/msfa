<?php

namespace App\Enums;

class LogBatchJob extends Enum
{
    const RETURN_SUCCESS = 0;
    const RETURN_FAIL = 240;
    const RETURN_PARAMETER_INVALID = 250;

    const ACTION_INSERT = 'INSERTED';
    const ACTION_UPDATE = 'UPDATED';
    const ACTION_DELETE = 'DELETED';

    const TYPE_API = 'api';
    const TYPE_BATCH_JOB = 'batch';
}