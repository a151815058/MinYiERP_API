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
    /*判斷日期格式*/
    public static function isValidDate($value): bool
    {
        $format = 'Y/m/d';
        $dateTime = \DateTime::createFromFormat($format, $value);
        return $dateTime && $dateTime->format($format) === $value;
    }
    /*判斷時間格式*/
    public static function isValidTime($value): bool
    {
        $format = 'H:i:s';
        $dateTime = \DateTime::createFromFormat($format, $value);
        return $dateTime && $dateTime->format($format) === $value;
    }
}