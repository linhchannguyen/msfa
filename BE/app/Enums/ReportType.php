<?php

namespace App\Enums;

class ReportType extends Enum
{
    const STATUS_CREATED = 10;
    const STATUS_SUBMIT = 20;
    const STATUS_APPROVAL = 30;

    const TYPE_NON_REGISTER = 1;
    const TYPE_REGISTER = 2;
    const TYPE_SUBMIT = 3;
    const TYPE_APPROVAL = 4;
    const TYPE_REMAND = 5;
    
    // Map status with language file in /resources/lang/file.php
    const __DESCRIPTIONS__ = [
        'TYPE_NON_REGISTER' => 'report.non_register',
        'TYPE_REGISTER' => 'report.register',
        'TYPE_SUBMIT' => 'report.submit',
        'TYPE_APPROVAL' => 'report.aprroval',
        'TYPE_REMAND' => 'report.remand'
    ];
}
