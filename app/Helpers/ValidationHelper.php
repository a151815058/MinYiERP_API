<?php

namespace App\Helpers;

/** @Ignore */ // 告訴 swagger-php 忽略這個 class
class ValidationHelper
{
    //判斷不能存在空白、""、''、"、'、*
    public static function isValidText($value): bool
    {
        $trimmed = trim($value);
        return !(
            $trimmed === '' ||
            $trimmed === '""' || $trimmed === "''" ||
            $trimmed === '"'  || $trimmed === "'"  ||
            $trimmed === "*"  || str_contains($trimmed,'*')
        ); 
    }
}