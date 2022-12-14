<?php

namespace App\Services;

class Util
{
    public function __construct()
    {
        defined('UTF8_BOM') || define('UTF8_BOM', chr(0xEF) . chr(0xBB) . chr(0xBF));
        defined('UTF16_LITTLE_ENDIAN_BOM') || define('UTF16_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE));
        defined('UTF16_BIG_ENDIAN_BOM') || define('UTF16_BIG_ENDIAN_BOM', chr(0xFE) . chr(0xFF));
        defined('UTF32_LITTLE_ENDIAN_BOM') || define('UTF32_LITTLE_ENDIAN_BOM', chr(0xFF) . chr(0xFE) . chr(0x00) . chr(0x00)); // @phpcs-ignore-line
        defined('UTF32_BIG_ENDIAN_BOM') || define('UTF32_BIG_ENDIAN_BOM', chr(0x00) . chr(0x00) . chr(0xFE) . chr(0xFF));  // @phpcs-ignore-line
    }

    public function detectUTFEncoding(?string $str): ?string
    {
        switch (substr($str, 0, 2)) {
            case UTF16_BIG_ENDIAN_BOM:
                return 'UTF-16BE';

            case UTF16_LITTLE_ENDIAN_BOM:
                return 'UTF-16LE';
        }

        if (substr($str, 0, 3) === UTF8_BOM) {
            return 'UTF-8';
        }

        return match (substr($str, 0, 4)) {
            UTF32_BIG_ENDIAN_BOM => 'UTF-32BE',
            UTF32_LITTLE_ENDIAN_BOM => 'UTF-32LE',
            default => null,
        };
    }
}
