<?php

namespace App\Enums;

class StatusResponse extends Enum
{
    const SUCCESS = 200;
    const CREATED = 201;
    const NO_CONTENT = 204;

    const VALIDATE_ERROR = 400;
    const VALIDATE_FORBIDEN = 403;
    const VALIDATE_NOT_FOUND = 404;
    const VALIDATE_UNAUTHORIZE = 401;
    const VALIDATE_SYSTEM_ERROR = 500;
}