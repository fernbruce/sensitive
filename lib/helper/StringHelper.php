<?php


class StringHelper
{
    public static function byteLength($string)
    {
        return mb_strlen((string)$string, '8bit');
    }
}