<?php

namespace App\Enums;

class FileUseType extends Enum
{
    const TYPE_CONVENTION = 'convention';
    const TYPE_PROFILE = 'profile';
    const TYPE_DOCUMENT = 'document';

    // Map status with language file in /resources/lang/file.php
    const __DESCRIPTIONS__ = [
        'TYPE_CONVENTION' => 'file.convention_text',
        'TYPE_PROFILE' => 'file.profile_text',
        'TYPE_DOCUMENT' => 'file.document_text'
    ];
}