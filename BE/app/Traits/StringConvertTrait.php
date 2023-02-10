<?php

namespace App\Traits;

trait StringConvertTrait
{
    public function convert_kana($string)
    {
        return mb_convert_kana($string,'s');
    }
}
