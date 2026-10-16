<?php

declare(strict_types=1);

namespace Royfee\Support;

class Str
{
    /**
     * Unicode invisible and special whitespace characters.
     *
     * @var string
     */
    protected const INVISIBLE_CHARACTERS = '\x{0009}\x{0020}\x{00A0}\x{00AD}\x{034F}\x{061C}\x{115F}\x{1160}\x{17B4}\x{17B5}\x{180E}\x{2000}\x{2001}\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{2007}\x{2008}\x{2009}\x{200A}\x{200B}\x{200C}\x{200D}\x{200E}\x{200F}\x{202F}\x{205F}\x{2060}\x{2061}\x{2062}\x{2063}\x{2064}\x{2065}\x{206A}\x{206B}\x{206C}\x{206D}\x{206E}\x{206F}\x{3000}\x{2800}\x{3164}\x{FEFF}\x{FFA0}\x{1D159}\x{1D173}\x{1D174}\x{1D175}\x{1D176}\x{1D177}\x{1D178}\x{1D179}\x{1D17A}\x{E0020}';

    /**
     * The cache of snake-cased strings.
     *
     * @var array<string, string>
     */
    protected static $snakeCache = [];

    /**
     * The cache of studly-cased strings.
     *
     * @var array<string, string>
     */
    protected static $studlyCache = [];

    /**
     * The cache of camel-cased strings.
     *
     * @var array<string, string>
     */
    protected static $camelCache = [];

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
            if ($needle !== '' && strpos($haystack, $needle) !== false) {
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
        // Capitalize the first letter of each word, preserving case of other characters
        return preg_replace_callback('/(?<=^|\s|\-|_)\p{L}/u', function ($matches) {
            return mb_strtoupper($matches[0], 'UTF-8');
        }, $value) ?? $value;
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
    public static function charAt(string $subject, int $index)
    {
        $length = mb_strlen($subject);

        if ($index < 0 ? $index < -$length : $index > $length - 1) {
            return false;
        }

        return mb_substr($subject, $index, 1);
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array<string>  $needles
     * @return bool
     */
    public static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param  string  $haystack
     * @param  string|array<string>  $needles
     * @return bool
     */
    public static function endsWith(string $haystack, $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle !== '' && substr($haystack, -strlen($needle)) === $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * Convert a string to snake_case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value . $delimiter;

        if (isset(static::$snakeCache[$key])) {
            return static::$snakeCache[$key];
        }

        if (! ctype_lower($value)) {
            // Insert delimiter between lowercase/digit and uppercase
            $value = preg_replace('/(\p{Ll}|\p{N})(\p{Lu})/u', '$1' . $delimiter . '$2', $value) ?? $value;

            // Insert delimiter between consecutive uppercase followed by lowercase
            $value = preg_replace('/(\p{Lu}+)(\p{Lu}\p{Ll})/u', '$1' . $delimiter . '$2', $value) ?? $value;

            // Replace spaces with delimiter
            $value = preg_replace('/\s+/u', $delimiter, $value) ?? $value;

            $value = mb_strtolower($value, 'UTF-8');
        }

        return static::$snakeCache[$key] = $value;
    }

    /**
     * Convert a string to camelCase.
     *
     * @param  string  $value
     * @return string
     */
    public static function camel(string $value): string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }

        return static::$camelCache[$value] = static::lcfirst(static::studly($value));
    }

    /**
     * Convert a string to StudlyCase (PascalCase).
     *
     * @param  string  $value
     * @return string
     */
    public static function studly(string $value): string
    {
        if (isset(static::$studlyCache[$value])) {
            return static::$studlyCache[$value];
        }

        $words = explode(' ', str_replace(['-', '_'], ' ', $value));

        $studly = '';

        foreach ($words as $word) {
            $studly .= static::ucfirst($word);
        }

        return static::$studlyCache[$value] = $studly;
    }

    /**
     * Convert a string to kebab-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function kebab(string $value): string
    {
        // Insert hyphens before uppercase letters (handles Unicode)
        $value = preg_replace('/([\p{Ll}])([\p{Lu}])/u', '$1-$2', $value) ?? $value;

        // Convert to lowercase
        $value = mb_strtolower($value, 'UTF-8');

        // Replace any non-alphanumeric characters with hyphens
        $value = preg_replace('/[^\p{L}\p{N}]+/u', '-', $value) ?? $value;

        // Remove leading and trailing hyphens
        return trim($value, '-');
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param  string  $title
     * @param  string  $separator
     * @param  string  $language
     * @return string
     */
    public static function slug(string $title, string $separator = '-', string $language = 'en'): string
    {
        // Transliterate Unicode to ASCII first
        $title = static::ascii($title, $language);

        $title = mb_strtolower($title, 'UTF-8');

        // Replace non-alphanumeric characters with separator
        $title = preg_replace('/[^a-z0-9\s-]/', '', $title) ?? $title;
        $title = preg_replace('/[\s-]+/', $separator, $title) ?? $title;

        return trim($title, $separator);
    }

    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $subject
     * @return string
     */
    public static function replaceFirst(string $search, string $replace, string $subject): string
    {
        $position = strpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $subject
     * @return string
     */
    public static function replaceLast(string $search, string $replace, string $subject): string
    {
        $position = strrpos($subject, $search);

        if ($position !== false) {
            return substr_replace($subject, $replace, $position, strlen($search));
        }

        return $subject;
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Strip whitespace (or other characters) from the beginning and end of a string.
     *
     * @param  string  $string
     * @param  string  $characters
     * @return string
     */
    public static function trim(string $string, ?string $characters = null): string
    {
        if ($characters === null) {
            $whitespace = '[\s' . static::INVISIBLE_CHARACTERS . ']';

            return preg_replace('/^' . $whitespace . '+|' . $whitespace . '+$/u', '', $string) ?? trim($string);
        }

        $quoted = preg_quote($characters, '/');

        return preg_replace('/^[' . $quoted . ']+|[' . $quoted . ']+$/u', '', $string) ?? $string;
    }

    /**
     * Strip whitespace from the beginning of a string.
     *
     * @param  string  $string
     * @param  string  $characters
     * @return string
     */
    public static function ltrim(string $string, ?string $characters = null): string
    {
        if ($characters === null) {
            $whitespace = '[\s' . static::INVISIBLE_CHARACTERS . ']';

            return preg_replace('/^' . $whitespace . '+/u', '', $string) ?? ltrim($string);
        }

        $quoted = preg_quote($characters, '/');

        return preg_replace('/^[' . $quoted . ']+/u', '', $string) ?? $string;
    }

    /**
     * Strip whitespace from the end of a string.
     *
     * @param  string  $string
     * @param  string  $characters
     * @return string
     */
    public static function rtrim(string $string, ?string $characters = null): string
    {
        if ($characters === null) {
            $whitespace = '[\s' . static::INVISIBLE_CHARACTERS . ']';

            return preg_replace('/' . $whitespace . '+$/u', '', $string) ?? rtrim($string);
        }

        $quoted = preg_quote($characters, '/');

        return preg_replace('/[' . $quoted . ']+$/u', '', $string) ?? $string;
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int  $limit
     * @param  string  $end
     * @return string
     */
    public static function limit(string $value, int $limit = 100, string $end = '...'): string
    {
        if (mb_strlen($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')) . $end;
    }

    /**
     * Limit the number of words in a string.
     *
     * @param  string  $value
     * @param  int  $words
     * @param  string  $end
     * @return string
     */
    public static function words(string $value, int $words = 100, string $end = '...'): string
    {
        preg_match('/^\s*+(?:\S++\s*+){1,' . $words . '}/u', $value, $matches);

        if (! isset($matches[0]) || mb_strlen($value, 'UTF-8') === mb_strlen($matches[0], 'UTF-8')) {
            return $value;
        }

        return rtrim($matches[0]) . $end;
    }

    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string|array<string>  $pattern
     * @param  string  $value
     * @return bool
     */
    public static function is($pattern, string $value): bool
    {
        $patterns = (array) $pattern;

        foreach ($patterns as $pattern) {
            if ($pattern === $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into wildcard patterns
            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^' . $pattern . '\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function before(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $result = strstr($subject, $search, true);

        return $result === false ? $subject : $result;
    }

    /**
     * Get the portion of a string after the first occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function after(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = strpos($subject, $search);

        return $pos === false ? $subject : substr($subject, $pos + strlen($search));
    }

    /**
     * Get the portion of a string between two given values.
     *
     * @param  string  $subject
     * @param  string  $from
     * @param  string  $to
     * @return string
     */
    public static function between(string $subject, string $from, string $to): string
    {
        if ($from === '') {
            return static::before($subject, $to);
        }

        if ($to === '') {
            return static::after($subject, $from);
        }

        $afterFrom = static::after($subject, $from);

        // If $from was not found, after() returns the entire subject
        if ($afterFrom === $subject && strpos($subject, $from) === false) {
            return '';
        }

        return static::before($afterFrom, $to);
    }

    /**
     * Remove all whitespace from both ends of a string and return.
     * Alias of trim for readability.
     *
     * @param  string  $string
     * @return string
     */
    public static function clean(string $string): string
    {
        return static::trim($string);
    }

    /**
     * Determine if a given string is empty.
     *
     * @param  string  $value
     * @return bool
     */
    public static function isEmpty(string $value): bool
    {
        return trim($value) === '';
    }

    /**
     * Determine if a given string is not empty.
     *
     * @param  string  $value
     * @return bool
     */
    public static function isNotEmpty(string $value): bool
    {
        return ! static::isEmpty($value);
    }

    /**
     * Get the string after the last occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function afterLast(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = strrpos($subject, $search);

        return $pos === false ? $subject : substr($subject, $pos + strlen($search));
    }

    /**
     * Get the string before the last occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function beforeLast(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }

        $pos = strrpos($subject, $search);

        return $pos === false ? $subject : substr($subject, 0, $pos);
    }

    /**
     * Determine if a given string contains all of the given substrings.
     *
     * @param  string  $haystack
     * @param  array<string>  $needles
     * @return bool
     */
    public static function containsAll(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (! static::contains($haystack, $needle)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determine if a given string does not contain a given substring.
     *
     * @param  string  $haystack
     * @param  string|array<string>  $needles
     * @return bool
     */
    public static function doesntContain(string $haystack, $needles): bool
    {
        return ! static::contains($haystack, $needles);
    }

    /**
     * Remove all extra whitespace from a string.
     *
     * @param  string  $value
     * @return string
     */
    public static function squish(string $value): string
    {
        return preg_replace('/[\s\x{00A0}\x{1680}\x{2000}-\x{200A}\x{2028}\x{2029}\x{202F}\x{205F}\x{3000}\x{FEFF}]+/u', ' ', trim($value)) ?? $value;
    }

    /**
     * Reverse a string (multibyte safe).
     *
     * @param  string  $value
     * @return string
     */
    public static function reverse(string $value): string
    {
        $result = '';
        $length = mb_strlen($value, 'UTF-8');

        for ($i = $length - 1; $i >= 0; $i--) {
            $result .= mb_substr($value, $i, 1, 'UTF-8');
        }

        return $result;
    }

    /**
     * Pad the left side of a string.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    public static function padLeft(string $value, int $length, string $pad = ' '): string
    {
        $currentLen = mb_strlen($value, 'UTF-8');

        if ($currentLen >= $length) {
            return $value;
        }

        $padLen = mb_strlen($pad, 'UTF-8');
        $needed = $length - $currentLen;
        $padding = mb_substr(str_repeat($pad, (int) ceil($needed / max(1, $padLen))), 0, $needed, 'UTF-8');

        return $padding . $value;
    }

    /**
     * Pad the right side of a string.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    public static function padRight(string $value, int $length, string $pad = ' '): string
    {
        $currentLen = mb_strlen($value, 'UTF-8');

        if ($currentLen >= $length) {
            return $value;
        }

        $padLen = mb_strlen($pad, 'UTF-8');
        $needed = $length - $currentLen;
        $padding = mb_substr(str_repeat($pad, (int) ceil($needed / max(1, $padLen))), 0, $needed, 'UTF-8');

        return $value . $padding;
    }

    /**
     * Pad both sides of a string.
     *
     * @param  string  $value
     * @param  int  $length
     * @param  string  $pad
     * @return string
     */
    public static function padBoth(string $value, int $length, string $pad = ' '): string
    {
        $currentLen = mb_strlen($value, 'UTF-8');

        if ($currentLen >= $length) {
            return $value;
        }

        $padLen = mb_strlen($pad, 'UTF-8');
        $totalPad = $length - $currentLen;
        $repeatCount = (int) ceil($totalPad / max(1, $padLen));
        $padString = str_repeat($pad, $repeatCount);

        $leftPad = (int) floor($totalPad / 2);
        $rightPad = (int) ceil($totalPad / 2);

        $left = mb_substr($padString, 0, $leftPad, 'UTF-8');
        $right = mb_substr($padString, 0, $rightPad, 'UTF-8');

        return $left . $value . $right;
    }

    /**
     * Wrap a string with given before and after strings.
     *
     * @param  string  $value
     * @param  string  $before
     * @param  string  $after
     * @return string
     */
    public static function wrap(string $value, string $before, string $after = ''): string
    {
        return $before . $value . ($after ?: $before);
    }

    /**
     * Unwrap a string by removing the given before and after strings.
     *
     * @param  string  $value
     * @param  string  $before
     * @param  string  $after
     * @return string
     */
    public static function unwrap(string $value, string $before, string $after = ''): string
    {
        if ($before !== '' && static::startsWith($value, $before)) {
            $value = substr($value, strlen($before));
        }

        $after = $after ?: $before;

        if ($after !== '' && static::endsWith($value, $after)) {
            $value = substr($value, 0, -strlen($after));
        }

        return (string) $value;
    }

    /**
     * Mask a portion of a string with a repeated character.
     *
     * @param  string  $string
     * @param  string  $character
     * @param  int  $index
     * @param  int|null  $length
     * @param  string  $encoding
     * @return string
     */
    public static function mask(string $string, string $character, int $index = 0, ?int $length = null, string $encoding = 'UTF-8'): string
    {
        if ($character === '') {
            return $string;
        }

        $mbLen = mb_strlen($string, $encoding);

        if (is_null($length) && $index >= 0) {
            $length = $mbLen;
        } elseif ($length === null) {
            $length = abs($index);
        }

        if ($index < 0) {
            $index = max(0, $mbLen + $index);
        }

        $segment = mb_substr($string, $index, $length, $encoding);
        $maskLen = mb_strlen($segment, $encoding);
        $mask = str_repeat(mb_substr($character, 0, 1, $encoding), $maskLen);

        return mb_substr($string, 0, $index, $encoding) . $mask . mb_substr($string, $index + $maskLen, null, $encoding);
    }

    /**
     * Determine if a given string is valid JSON.
     *
     * @param  string  $value
     * @return bool
     */
    public static function isJson(string $value): bool
    {
        if ($value === '') {
            return false;
        }

        json_decode($value);

        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Determine if a given string is a valid URL.
     *
     * @param  string  $value
     * @return bool
     */
    public static function isUrl(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Remove a substring from a string.
     *
     * @param  string|array<string>  $search
     * @param  string  $subject
     * @param  bool  $caseSensitive
     * @return string
     */
    public static function remove($search, string $subject, bool $caseSensitive = true): string
    {
        $searches = (array) $search;

        if ($caseSensitive) {
            return str_replace($searches, '', $subject);
        }

        return str_ireplace($searches, '', $subject);
    }

    /**
     * Replace occurrences of a search string with a replacement.
     *
     * @param  string|array<string>  $search
     * @param  string|array<string>  $replace
     * @param  string  $subject
     * @param  bool  $caseSensitive
     * @return string
     */
    public static function replace($search, $replace, string $subject, bool $caseSensitive = true): string
    {
        $searches = (array) $search;
        $replacements = (array) $replace;

        if (count($replacements) < count($searches)) {
            $replacements = array_fill(0, count($searches), $replacements[0] ?? '');
        }

        if ($caseSensitive) {
            return str_replace($searches, $replacements, $subject);
        }

        return str_ireplace($searches, $replacements, $subject);
    }

    /**
     * Perform a regex match and return the first captured group.
     *
     * @param  string  $pattern
     * @param  string  $subject
     * @return string
     */
    public static function match(string $pattern, string $subject): string
    {
        preg_match($pattern, $subject, $matches);

        return $matches[1] ?? $matches[0] ?? '';
    }

    /**
     * Capitalize the first letter of a string.
     *
     * @param  string  $string
     * @return string
     */
    public static function ucfirst(string $string): string
    {
        return mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, null, 'UTF-8');
    }

    /**
     * Make the first letter of a string lowercase.
     *
     * @param  string  $string
     * @return string
     */
    public static function lcfirst(string $string): string
    {
        return mb_strtolower(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, null, 'UTF-8');
    }

    /**
     * Get the number of occurrences of a substring.
     *
     * @param  string  $haystack
     * @param  string  $needle
     * @return int
     */
    public static function substrCount(string $haystack, string $needle): int
    {
        return mb_substr_count($haystack, $needle, 'UTF-8');
    }

    /**
     * Encode a string to Base64.
     *
     * @param  string  $string
     * @return string
     */
    public static function toBase64(string $string): string
    {
        return base64_encode($string);
    }

    /**
     * Decode a Base64 string.
     *
     * @param  string  $string
     * @return string|false
     */
    public static function fromBase64(string $string)
    {
        return base64_decode($string, true);
    }

    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param  string  $value
     * @param  string  $language
     * @return string
     */
    public static function ascii(string $value, string $language = 'en'): string
    {
        $asciiMap = static::asciiMap();

        // Use strtr for one-pass replacement (handles multi-char keys correctly)
        $value = strtr($value, $asciiMap);

        // Remove remaining non-ASCII characters
        return preg_replace('/[^\x20-\x7E]/', '', $value) ?? $value;
    }

    /**
     * Convert the given string to title case for each word (headline style).
     *
     * @param  string  $value
     * @return string
     */
    public static function headline(string $value): string
    {
        // Use snake() to handle all naming styles (camelCase, snake_case, kebab-case)
        $value = static::snake($value, ' ');

        // Replace any remaining delimiters with spaces
        $value = static::replace(['-', '_'], ' ', $value);

        return static::title(mb_strtolower(trim($value), 'UTF-8'));
    }

    /**
     * Get the flat ASCII character map (unicode char => ascii replacement).
     * Cached after first call for performance.
     *
     * @return array<string, string>
     */
    protected static function asciiMap(): array
    {
        static $asciiMap;

        if (isset($asciiMap)) {
            return $asciiMap;
        }

        $asciiMap = [];

        foreach (static::charsArray() as $ascii => $unicodeChars) {
            $length = mb_strlen($unicodeChars, 'UTF-8');
            for ($i = 0; $i < $length; $i++) {
                $char = mb_substr($unicodeChars, $i, 1, 'UTF-8');
                $asciiMap[$char] = $ascii;
            }
        }

        return $asciiMap;
    }

    /**
     * Get the ASCII character mapping array.
     *
     * @return array<string, string>
     */
    protected static function charsArray(): array
    {
        static $charsArray;

        if (isset($charsArray)) {
            return $charsArray;
        }

        return $charsArray = [
            // Multi-char replacements first (before single-char that overlap)
            'AE' => 'ǼÆ',
            'OE' => 'Œ',
            'ss' => 'ß',
            '0' => '°₀⁰',
            '1' => '¹₁¹',
            '2' => '²₂²',
            '3' => '³₃³',
            '4' => '⁴₄⁴',
            '5' => '⁵₅⁵',
            '6' => '⁶₆⁶',
            '7' => '⁷₇⁷',
            '8' => '⁸₈⁸',
            '9' => '⁹₉⁹',
            'A' => 'ÀÁÂÃÄÅǺĀĂĄǍΑΆẢẠΑАḀȀȂ',
            'B' => 'ΒΒḂḄḆ',
            'C' => 'ÇĆĈĊČℂℭḈ',
            'D' => 'ÐĎĐḊḌḎḐḒⅅⅮ',
            'E' => 'ÈÉÊËĒĔĖĘĚΕΈẼẺẸΕЕḔḖḘḚḜẸ',
            'F' => 'ƑΦḞ',
            'G' => 'ĜĞĠĢΓҐḠ',
            'H' => 'ĤĦΗΉḢḤḦḨḪ',
            'I' => 'ÌÍÎÏĨĪĬĮİΙΊΪỈỊІḬ',
            'J' => 'Ĵ',
            'K' => 'ĶΚḰḲḴ',
            'L' => 'ĹĻĽĿΛḶḸḺḼ',
            'M' => 'ΜḾṀṂ',
            'N' => 'ÑŃŅŇΝŊṄṆṈṊ',
            'O' => 'ÒÓÔÕÖŌŎŐΟΌΩΏỎỌΟОṌṎṐṒ',
            'P' => 'ΠṔṖ',
            'R' => 'ŔŖŘΡṘṚṜṞ',
            'S' => 'ŚŜŞŠΣṠṢṤṦ',
            'T' => 'ŢŤŦΤṪṬṮṰ',
            'U' => 'ÙÚÛÜŨŪŬŮŰŲƯǓỦỤŪṲṴṶṸṺ',
            'V' => 'ΒṼṾ',
            'W' => 'ŴΩΏẀẂẄẆẈ',
            'X' => 'ΧẊẌ',
            'Y' => 'ÝŶŸΥΎΫỲỸỶỴ',
            'Z' => 'ŹŻŽΖẐẒẔ',
            'a' => 'àáâãäåǻāăąǎªαάảạаȁȃ',
            'ae' => 'ǽæ',
            'b' => 'βḃḅḇ',
            'c' => 'çćĉċčℂℭḉ',
            'd' => 'ðďđḋḍḏḑḓⅆ',
            'e' => 'èéêëēĕėęěεέẽẻẹеḕḗḙḛḝ',
            'f' => 'ƒφḟ',
            'g' => 'ĝğġģγґḡ',
            'h' => 'ĥħηήḣḥḧḩḫẖ',
            'i' => 'ìíîïĩīĭįιίϊỉịіḭ',
            'j' => 'ĵ',
            'k' => 'ķκḱḳḵ',
            'l' => 'ĺļľŀλḷḹḻḽ',
            'm' => 'μḿṁṃ',
            'n' => 'ñńņňŉνŋṅṇṉṋ',
            'o' => 'òóôõöōŏőοόωώỏọоṍṏṑṓ',
            'p' => 'πṕṗ',
            'r' => 'ŕŗřρṙṛṝṟ',
            's' => 'śŝşšςσṡṣṥṧ',
            't' => 'ţťŧτṫṭṯṱẗ',
            'u' => 'ùúûüũūŭůűųưǔủụṳṵṷṹṻ',
            'v' => 'βṽỿ',
            'w' => 'ŵωώẁẃẅẇẉẘ',
            'x' => 'χẋẍ',
            'y' => 'ýÿŷυύϋύỳỹỷỵ',
            'z' => 'źżžζẑẓẕ',
        ];
    }
}
