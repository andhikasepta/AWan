<?php

/**
 * Sanitize a string to ensure valid UTF-8 encoding for PostgreSQL.
 * Removes 0xa0 bytes and any other invalid UTF-8 sequences.
 *
 * @param string|null $str
 * @return string
 */
function sanitize_utf8(?string $str): string
{
    if ($str === null || $str === '') {
        return '';
    }

    // Replace UTF-8 encoded NBSP (0xC2 0xA0)
    $str = str_replace("\xC2\xA0", ' ', $str);
    // Replace raw 0xA0 byte
    $str = str_replace("\xA0", ' ', $str);

    // Convert to valid UTF-8, dropping invalid sequences
    $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');

    return $str;
}
