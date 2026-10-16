<?php

declare(strict_types=1);

namespace Royfee\Support;

use InvalidArgumentException;

class Num
{
    // =========================================================================
    // Range & Constraint
    // =========================================================================

    /**
     * Clamp a value between a minimum and maximum.
     *
     * @param  int|float  $value
     * @param  int|float  $min
     * @param  int|float  $max
     * @return int|float
     */
    public static function clamp($value, $min, $max)
    {
        if ($value < $min) {
            return $min;
        }

        if ($value > $max) {
            return $max;
        }

        return $value;
    }

    /**
     * Determine if a value is between a minimum and maximum (inclusive).
     *
     * @param  int|float  $value
     * @param  int|float  $min
     * @param  int|float  $max
     * @return bool
     */
    public static function between($value, $min, $max): bool
    {
        return $value >= $min && $value <= $max;
    }

    // =========================================================================
    // Formatting
    // =========================================================================

    /**
     * Format a number with grouped thousands.
     *
     * @param  int|float  $number
     * @param  int  $decimals
     * @param  string  $decPoint
     * @param  string  $thousandsSep
     * @return string
     */
    public static function format($number, int $decimals = 2, string $decPoint = '.', string $thousandsSep = ','): string
    {
        return number_format((float) $number, $decimals, $decPoint, $thousandsSep);
    }

    /**
     * Format a number as currency.
     *
     * @param  int|float|string  $amount
     * @param  string  $symbol
     * @param  int  $decimals
     * @return string
     */
    public static function currency($amount, string $symbol = '$', int $decimals = 2): string
    {
        return $symbol . static::format((float) $amount, $decimals);
    }

    /**
     * Format a number as a percentage string.
     *
     * @param  int|float  $value
     * @param  int  $decimals
     * @return string
     */
    public static function percent($value, int $decimals = 0): string
    {
        return number_format((float) $value * 100, $decimals) . '%';
    }

    /**
     * Convert bytes to a human-readable file size string.
     *
     * @param  int|float  $bytes
     * @param  int  $decimals
     * @return string
     */
    public static function fileSize($bytes, int $decimals = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = (int) floor(log((float) $bytes, 1024));
        $factor = min($factor, count($units) - 1);

        return sprintf("%.{$decimals}f %s", (float) $bytes / pow(1024, $factor), $units[$factor]);
    }

    // =========================================================================
    // Statistical Calculations
    // =========================================================================

    /**
     * Calculate the average of values using bcmath for precision.
     *
     * @param  array<int|float|string>  $values
     * @param  int  $scale  Decimal precision
     * @return string
     */
    public static function average(array $values, int $scale = 2): string
    {
        if (empty($values)) {
            throw new InvalidArgumentException('Cannot calculate average of an empty array.');
        }

        $stringValues = array_map(function ($v) {
            return (string) $v;
        }, $values);

        return bcdiv(self::preciseSum($stringValues, $scale), (string) count($values), $scale) ?? '0';
    }

    /**
     * Calculate the median of values.
     *
     * @param  int|float  ...$values
     * @return float
     */
    public static function median(...$values): float
    {
        if (empty($values)) {
            throw new InvalidArgumentException('Cannot calculate median of an empty set.');
        }

        sort($values);
        $count = count($values);
        $middle = (int) floor($count / 2);

        if ($count % 2 === 0) {
            return ($values[$middle - 1] + $values[$middle]) / 2;
        }

        return (float) $values[$middle];
    }

    /**
     * Calculate the sum of an array using bcmath for precision.
     *
     * @param  array<int|float|string>  $values
     * @param  int  $scale  Decimal precision
     * @return string
     */
    public static function sum(array $values, int $scale = 2): string
    {
        $stringValues = array_map(function ($v) {
            return (string) $v;
        }, $values);

        return self::preciseSum($stringValues, $scale);
    }

    /**
     * Calculate the product of an array.
     *
     * @param  array<int|float>  $values
     * @return int|float
     */
    public static function product(array $values)
    {
        if (empty($values)) {
            throw new InvalidArgumentException('Cannot calculate product of an empty array.');
        }

        return array_product($values);
    }

    /**
     * Get the maximum value from an array.
     *
     * @param  array<int|float>  $values
     * @return int|float
     */
    public static function max(array $values)
    {
        if (empty($values)) {
            throw new InvalidArgumentException('Cannot get max of an empty array.');
        }

        return max($values);
    }

    /**
     * Get the minimum value from an array.
     *
     * @param  array<int|float>  $values
     * @return int|float
     */
    public static function min(array $values)
    {
        if (empty($values)) {
            throw new InvalidArgumentException('Cannot get min of an empty array.');
        }

        return min($values);
    }

    /**
     * Round a value to a given precision using bcmath.
     *
     * @param  int|float|string  $value
     * @param  int  $precision
     * @return string
     */
    public static function round($value, int $precision = 0): string
    {
        return self::bcRound((string) $value, $precision);
    }

    /**
     * Round down to a given precision using bcmath.
     *
     * @param  int|float|string  $value
     * @param  int  $precision
     * @return string
     */
    public static function floor($value, int $precision = 0): string
    {
        return self::bcFloor((string) $value, $precision);
    }

    /**
     * Round up to a given precision using bcmath.
     *
     * @param  int|float|string  $value
     * @param  int  $precision
     * @return string
     */
    public static function ceil($value, int $precision = 0): string
    {
        return self::bcCeil((string) $value, $precision);
    }

    // =========================================================================
    // Type Checking
    // =========================================================================

    /**
     * Determine if a value is even.
     *
     * @param  int  $value
     * @return bool
     */
    public static function isEven(int $value): bool
    {
        return $value % 2 === 0;
    }

    /**
     * Determine if a value is odd.
     *
     * @param  int  $value
     * @return bool
     */
    public static function isOdd(int $value): bool
    {
        return $value % 2 !== 0;
    }

    /**
     * Determine if a value is positive.
     *
     * @param  int|float  $value
     * @return bool
     */
    public static function isPositive($value): bool
    {
        return $value > 0;
    }

    /**
     * Determine if a value is negative.
     *
     * @param  int|float  $value
     * @return bool
     */
    public static function isNegative($value): bool
    {
        return $value < 0;
    }

    /**
     * Determine if a value is zero.
     *
     * @param  int|float  $value
     * @return bool
     */
    public static function isZero($value): bool
    {
        return $value == 0;
    }

    /**
     * Determine if a value is numeric.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function isNumeric($value): bool
    {
        return is_numeric($value);
    }

    /**
     * Determine if a value is an integer (or a float with no fractional part).
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function isInt($value): bool
    {
        if (is_int($value)) {
            return true;
        }

        if (is_float($value)) {
            return floor($value) == $value;
        }

        if (is_string($value) && $value !== '') {
            return (string) (int) $value === $value;
        }

        return false;
    }

    /**
     * Determine if a value is a float.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function isFloat($value): bool
    {
        return is_float($value);
    }

    // =========================================================================
    // Math Operations
    // =========================================================================

    /**
     * Calculate the greatest common divisor.
     *
     * @param  int  $a
     * @param  int  $b
     * @return int
     */
    public static function gcd(int $a, int $b): int
    {
        $a = abs($a);
        $b = abs($b);

        while ($b !== 0) {
            $temp = $b;
            $b = $a % $b;
            $a = $temp;
        }

        return $a;
    }

    /**
     * Calculate the least common multiple.
     *
     * @param  int  $a
     * @param  int  $b
     * @return int
     */
    public static function lcm(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) {
            return 0;
        }

        return abs($a * $b) / static::gcd($a, $b);
    }

    /**
     * Calculate the factorial of a number.
     *
     * @param  int  $n
     * @return int
     * @throws InvalidArgumentException
     */
    public static function factorial(int $n): int
    {
        if ($n < 0) {
            throw new InvalidArgumentException('Factorial is not defined for negative numbers.');
        }

        if ($n <= 1) {
            return 1;
        }

        $result = 1;

        for ($i = 2; $i <= $n; $i++) {
            $result *= $i;
        }

        return $result;
    }

    /**
     * Get the nth Fibonacci number.
     *
     * @param  int  $n
     * @return int
     * @throws InvalidArgumentException
     */
    public static function fibonacci(int $n): int
    {
        if ($n < 0) {
            throw new InvalidArgumentException('Fibonacci is not defined for negative numbers.');
        }

        if ($n <= 1) {
            return $n;
        }

        $a = 0;
        $b = 1;

        for ($i = 2; $i <= $n; $i++) {
            $temp = $a + $b;
            $a = $b;
            $b = $temp;
        }

        return $b;
    }

    /**
     * Raise a base to a given exponent.
     *
     * @param  int|float  $base
     * @param  int|float  $exp
     * @return int|float
     */
    public static function power($base, $exp)
    {
        return pow($base, $exp);
    }

    /**
     * Calculate the square root of a value.
     *
     * @param  int|float  $value
     * @return float
     */
    public static function sqrt($value): float
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Square root is not defined for negative numbers.');
        }

        return sqrt((float) $value);
    }

    // =========================================================================
    // Random Numbers
    // =========================================================================

    /**
     * Generate a random integer within a given range.
     *
     * @param  int  $min
     * @param  int  $max
     * @return int
     */
    public static function random(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    /**
     * Generate a random float within a given range.
     *
     * @param  int|float  $min
     * @param  int|float  $max
     * @param  int  $decimals
     * @return float
     */
    public static function randomFloat($min = 0, $max = 1, int $decimals = 2): float
    {
        $value = $min + mt_rand() / mt_getrandmax() * ($max - $min);

        return round($value, $decimals);
    }

    // =========================================================================
    // Precise Calculations (bcmath) - For financial / amount scenarios
    // =========================================================================

    /**
     * Precise addition using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return string
     */
    public static function add($a, $b, int $scale = 2): string
    {
        return bcadd((string) $a, (string) $b, $scale);
    }

    /**
     * Precise subtraction using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return string
     */
    public static function sub($a, $b, int $scale = 2): string
    {
        return bcsub((string) $a, (string) $b, $scale);
    }

    /**
     * Precise multiplication using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return string
     */
    public static function mul($a, $b, int $scale = 2): string
    {
        return bcmul((string) $a, (string) $b, $scale);
    }

    /**
     * Precise division using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return string
     * @throws InvalidArgumentException
     */
    public static function div($a, $b, int $scale = 2): string
    {
        if (bccomp((string) $b, '0', $scale) === 0) {
            throw new InvalidArgumentException('Division by zero.');
        }

        return bcdiv((string) $a, (string) $b, $scale) ?? '0';
    }

    /**
     * Precise comparison using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return int  -1 if a < b, 0 if a == b, 1 if a > b
     */
    public static function comp($a, $b, int $scale = 2): int
    {
        return bccomp((string) $a, (string) $b, $scale);
    }

    /**
     * Precise equality check using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return bool
     */
    public static function eq($a, $b, int $scale = 2): bool
    {
        return static::comp($a, $b, $scale) === 0;
    }

    /**
     * Precise greater-than check using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return bool
     */
    public static function gt($a, $b, int $scale = 2): bool
    {
        return static::comp($a, $b, $scale) > 0;
    }

    /**
     * Precise less-than check using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return bool
     */
    public static function lt($a, $b, int $scale = 2): bool
    {
        return static::comp($a, $b, $scale) < 0;
    }

    /**
     * Precise greater-than-or-equal check using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return bool
     */
    public static function gte($a, $b, int $scale = 2): bool
    {
        return static::comp($a, $b, $scale) >= 0;
    }

    /**
     * Precise less-than-or-equal check using bcmath.
     *
     * @param  string|int  $a
     * @param  string|int  $b
     * @param  int  $scale  Decimal precision
     * @return bool
     */
    public static function lte($a, $b, int $scale = 2): bool
    {
        return static::comp($a, $b, $scale) <= 0;
    }

    /**
     * Convert a value to a decimal string with a given precision.
     *
     * @param  string|int|float  $value
     * @param  int  $scale  Decimal precision
     * @return string
     */
    public static function toDecimal($value, int $scale = 2): string
    {
        return bcadd((string) $value, '0', $scale);
    }

    // =========================================================================
    // Internal Helpers
    // =========================================================================

    /**
     * Precise sum of string values using bcmath.
     *
     * @param  array<string>  $values
     * @return string
     */
    private static function preciseSum(array $values, int $scale = 2): string
    {
        $sum = '0';

        foreach ($values as $value) {
            $sum = bcadd($sum, (string) $value, max($scale, 10));
        }

        return bcadd($sum, '0', $scale);
    }

    /**
     * Get the actual number of decimal places in a string value.
     *
     * @param  string  $value
     * @return int
     */
    private static function getActualScale(string $value): int
    {
        $dotPos = strpos($value, '.');

        if ($dotPos === false) {
            return 0;
        }

        return strlen($value) - $dotPos - 1;
    }

    /**
     * Precise rounding using bcmath (round half up).
     *
     * @param  string  $value
     * @param  int  $precision
     * @return string
     */
    private static function bcRound(string $value, int $precision): string
    {
        if ($precision < 0) {
            $precision = 0;
        }

        // Add 0.5 * 10^(-precision) for rounding
        $roundDigit = '0.' . str_repeat('0', $precision) . '5';

        if (bccomp($value, '0', $precision + 1) < 0) {
            $roundDigit = '-' . $roundDigit;
        }

        return bcadd($value, $roundDigit, $precision);
    }

    /**
     * Precise floor using bcmath.
     *
     * @param  string  $value
     * @param  int  $precision
     * @return string
     */
    private static function bcFloor(string $value, int $precision): string
    {
        if ($precision < 0) {
            $precision = 0;
        }

        // Truncate to precision
        $result = bcadd($value, '0', $precision);

        // Use actual decimal places for comparison to avoid precision loss
        $actualScale = self::getActualScale($value);
        $compScale = max($precision + 1, $actualScale);

        // For negative numbers with fractional part, subtract 1
        if (bccomp($value, $result, $compScale) < 0) {
            $result = bcsub($result, bcpow('10', (string) -$precision, $precision), $precision);
        }

        return $result;
    }

    /**
     * Precise ceiling using bcmath.
     *
     * @param  string  $value
     * @param  int  $precision
     * @return string
     */
    private static function bcCeil(string $value, int $precision): string
    {
        if ($precision < 0) {
            $precision = 0;
        }

        // Truncate to precision
        $result = bcadd($value, '0', $precision);

        // Use actual decimal places for comparison to avoid precision loss
        $actualScale = self::getActualScale($value);
        $compScale = max($precision + 1, $actualScale);

        // For positive numbers with fractional part, add 1 unit at precision
        if (bccomp($value, $result, $compScale) > 0) {
            $unit = bcpow('10', (string) -$precision, $precision);
            $result = bcadd($result, $unit, $precision);
        }

        return $result;
    }
}
