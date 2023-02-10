<?php

namespace App\Enums;

class ResponseCode extends Enum
{
    const FORBIDENT = 403;
    const NOT_ALLOWED = 405;
    const NOT_FOUND = 404;
    const SERVICE_UNAVAILABLE = 503;
    const INTERNAL_SERVER_ERROR = 500;
}