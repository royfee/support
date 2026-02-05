<?php

declare(strict_types=1);

namespace royfee\support;

class Str
{
    public static function substr(string $string, int $start, ?int $length = null): string
    {
        return mb_substr($string, $start, $length, 'UTF-8');
    }

    /**
     * @param string $haystack
     * @param array<string> $needles
     * @return boolean
     */
    public static function contains(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }
    public static function length(string $value, ?string $encoding = null): int
    {
        if (null !== $encoding) {
            return (int) mb_strlen($value, $encoding);
        }
        return (int) mb_strlen($value);
    }

    /**
     * Capitalize the First Letter
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    public static function encoding(string $string, string $to = 'utf-8', string $from = 'gb2312'): string
    {
        return (string) mb_convert_encoding($string, $to, $from);
    }

    /**
     * Random string
     */
    public static function random(int $length = 16): string
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = max(1, $length - $len);
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    /**
     * Add a suffix to a string if it does not already end with the suffix.
     */
    public static function finish(string $value, string $cap): string
    {
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:' . $quoted . ')+$/u', '', $value) . $cap;
    }

    /**
     * Get the character at the specified index.
     *
     * @param  string  $subject
     * @param  int  $index
     * @return string|false
     */
    public static function charAt($subject, $index)
    {
        $length = mb_strlen($subject);

        if ($index < 0 ? $index < -$length : $index > $length - 1) {
            return false;
        }

        return mb_substr($subject, $index, 1);
    }
}