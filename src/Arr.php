<?php

declare(strict_types=1);

namespace Royfee\Support;

use InvalidArgumentException;

class Arr
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param  array<mixed>  $array
     * @param  string|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get(array $array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (explode('.', (string) $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation.
     *
     * @param  array<mixed>  $array
     * @param  string|int|array<string|int>  $keys
     * @return bool
     */
    public static function has(array $array, $keys): bool
    {
        $keys = (array) $keys;

        if (empty($keys)) {
            return false;
        }

        foreach ($keys as $key) {
            $subArray = $array;

            if (array_key_exists($key, $array)) {
                continue;
            }

            foreach (explode('.', (string) $key) as $segment) {
                if (is_array($subArray) && array_key_exists($segment, $subArray)) {
                    $subArray = $subArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * @param  array<mixed>  $array
     * @param  string|int|null  $key
     * @param  mixed  $value
     * @return array<mixed>
     */
    public static function set(array &$array, $key, $value): array
    {
        if (is_null($key)) {
            return $array = (array) $value;
        }

        $keys = explode('.', (string) $key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param  array<mixed>  $array
     * @param  string|int|array<string|int>  $keys
     * @return void
     */
    public static function forget(array &$array, $keys): void
    {
        $keys = (array) $keys;

        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                unset($array[$key]);
                continue;
            }

            $parts = explode('.', (string) $key);
            $subArray = &$array;

            foreach ($parts as $i => $part) {
                if ($i === count($parts) - 1) {
                    if (array_key_exists($part, $subArray)) {
                        unset($subArray[$part]);
                    }
                } elseif (is_array($subArray) && array_key_exists($part, $subArray)) {
                    $subArray = &$subArray[$part];
                } else {
                    break;
                }
            }
        }
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array<mixed>  $array
     * @param  string|array<string>  $keys
     * @return array<mixed>
     */
    public static function only(array $array, $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * Get all of the given array except for a specified set of keys.
     *
     * @param  array<mixed>  $array
     * @param  string|array<string>  $keys
     * @return array<mixed>
     */
    public static function except(array $array, $keys): array
    {
        return array_diff_key($array, array_flip((array) $keys));
    }

    /**
     * Return the first element in an array passing a given truth test.
     *
     * @param  array<mixed>  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public static function first(array $array, ?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? $default : reset($array);
        }

        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array<mixed>  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public static function last(array $array, ?callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? $default : end($array);
        }

        return static::first(array_reverse($array), $callback, $default);
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array<mixed>  $array
     * @param  int  $depth
     * @return array<mixed>
     */
    public static function flatten(array $array, int $depth = PHP_INT_MAX): array
    {
        $result = [];

        foreach ($array as $item) {
            if (! is_array($item)) {
                $result[] = $item;
            } else {
                $values = $depth === 1
                    ? array_values($item)
                    : static::flatten($item, $depth - 1);

                foreach ($values as $value) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Filter the array using the given callback.
     *
     * @param  array<mixed>  $array
     * @param  callable  $callback
     * @return array<mixed>
     */
    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Pluck an array of values from an array.
     *
     * @param  array<mixed>  $array
     * @param  string|array<string>  $value
     * @param  string|null  $key
     * @return array<mixed>
     */
    public static function pluck(array $array, $value, ?string $key = null): array
    {
        $results = [];

        $valuePath = is_array($value) ? $value : explode('.', $value);

        foreach ($array as $item) {
            $itemValue = static::get((array) $item, implode('.', $valuePath));

            if (! is_null($key)) {
                $itemKey = static::get((array) $item, $key);
                $results[$itemKey] = $itemValue;
            } else {
                $results[] = $itemValue;
            }
        }

        return $results;
    }

    /**
     * Group an array by a given key.
     *
     * @param  array<mixed>  $array
     * @param  string|callable  $groupBy
     * @return array<mixed>
     */
    public static function groupBy(array $array, $groupBy): array
    {
        $result = [];

        foreach ($array as $key => $item) {
            if (is_string($groupBy)) {
                $groupKey = static::get((array) $item, $groupBy);
            } else {
                $groupKey = $groupBy($item, $key);
            }

            $result[$groupKey][] = $item;
        }

        return $result;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param  array<mixed>  $array
     * @param  string|int  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function pull(array &$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Convert the array to a query string compatible format.
     *
     * @param  array<mixed>  $array
     * @param  string  $numericPrefix
     * @return string
     */
    public static function query(array $array, string $numericPrefix = ''): string
    {
        return http_build_query($array, '', '&');
    }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }

    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  array<mixed>  $array
     * @return array<mixed>
     */
    public static function collapse(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            if (! is_array($values)) {
                continue;
            }

            $results[] = $values;
        }

        return array_merge([], ...$results);
    }

    /**
     * Push an item onto the beginning of an array.
     *
     * @param  array<mixed>  $array
     * @param  mixed  $value
     * @param  mixed  $key
     * @return array<mixed>
     */
    public static function prepend(array $array, $value, $key = null): array
    {
        if (func_num_args() === 2) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    /**
     * Get one or a specified number of random values from an array.
     *
     * @param  array<mixed>  $array
     * @param  int|null  $number
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function random(array $array, ?int $number = null)
    {
        $requested = is_null($number) ? 1 : $number;

        $count = count($array);

        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }

        if (is_null($number)) {
            return $array[array_rand($array)];
        }

        if ($number === 0) {
            return [];
        }

        $keys = array_rand($array, $number);

        $results = [];

        foreach ((array) $keys as $key) {
            $results[] = $array[$key];
        }

        return $results;
    }

    /**
     * Shuffle an array.
     *
     * @param  array<mixed>  $array
     * @return array<mixed>
     */
    public static function shuffle(array $array): array
    {
        shuffle($array);

        return $array;
    }

    /**
     * Sort an array by key.
     *
     * @param  array<mixed>  $array
     * @param  int  $options
     * @param  bool  $descending
     * @return array<mixed>
     */
    public static function sort(array $array, int $options = SORT_REGULAR, bool $descending = false): array
    {
        if ($descending) {
            arsort($array, $options);
        } else {
            asort($array, $options);
        }

        return $array;
    }

    /**
     * Recursively sort an array by keys and values.
     *
     * @param  array<mixed>  $array
     * @param  int  $options
     * @param  bool  $descending
     * @return array<mixed>
     */
    public static function sortRecursive(array $array, int $options = SORT_REGULAR, bool $descending = false): array
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value, $options, $descending);
            }
        }

        if ($descending) {
            krsort($array, $options);
        } else {
            ksort($array, $options);
        }

        return $array;
    }

    /**
     * Convert a flatten "dot" notation array into an array.
     *
     * @param  array<mixed>  $array
     * @return array<mixed>
     */
    public static function undot(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            static::set($result, $key, $value);
        }

        return $result;
    }

    /**
     * Wrap the given value in an array. If it's already an array, return it as-is.
     *
     * @param  mixed  $value
     * @return array<mixed>
     */
    public static function wrap($value): array
    {
        if (is_null($value)) {
            return [];
        }

        return is_array($value) ? $value : [$value];
    }

    /**
     * Check if the array is an associative array.
     *
     * @param  array<mixed>  $array
     * @return bool
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);

        return array_keys($keys) !== $keys;
    }

    /**
     * Check if the array is a list (sequential numeric keys).
     *
     * @param  array<mixed>  $array
     * @return bool
     */
    public static function isList(array $array): bool
    {
        return ! static::isAssoc($array);
    }

    /**
     * Determine whether the given key exists in the array.
     *
     * @param  array<mixed>  $array
     * @param  string|int  $key
     * @return bool
     */
    public static function exists(array $array, $key): bool
    {
        return array_key_exists($key, $array);
    }

    /**
     * Determine if any of the given keys exist in the array.
     *
     * @param  array<mixed>  $array
     * @param  string|array<string|int>  $keys
     * @return bool
     */
    public static function hasAny(array $array, $keys): bool
    {
        $keys = (array) $keys;

        foreach ($keys as $key) {
            if (static::has($array, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add an element to an array if it doesn't exist.
     *
     * @param  array<mixed>  $array
     * @param  string|int  $key
     * @param  mixed  $value
     * @return array<mixed>
     */
    public static function add(array $array, $key, $value): array
    {
        if (is_null(static::get($array, $key))) {
            static::set($array, $key, $value);
        }

        return $array;
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param  array<mixed>  $array
     * @param  string  $prepend
     * @return array<mixed>
     */
    public static function dot(array $array, string $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && ! empty($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }

    /**
     * Split an array into two arrays: one with keys and one with values.
     *
     * @param  array<mixed>  $array
     * @return array{0: array<mixed>, 1: array<mixed>}
     */
    public static function divide(array $array): array
    {
        return [array_keys($array), array_values($array)];
    }

    /**
     * Get the first or last number of items from an array.
     *
     * @param  array<mixed>  $array
     * @param  int  $limit
     * @return array<mixed>
     */
    public static function take(array $array, int $limit): array
    {
        if ($limit < 0) {
            return array_slice($array, $limit, abs($limit));
        }

        return array_slice($array, 0, $limit);
    }

    /**
     * Run a map over each of the items in the array.
     *
     * @param  array<mixed>  $array
     * @param  callable  $callback
     * @return array<mixed>
     */
    public static function map(array $array, callable $callback): array
    {
        $keys = array_keys($array);

        $items = array_map($callback, $array, $keys);

        return array_combine($keys, $items) ?: [];
    }

    /**
     * Run a map over each key/value pair and flatten into a single array.
     *
     * @param  array<mixed>  $array
     * @param  callable  $callback
     * @return array<mixed>
     */
    public static function mapWithKeys(array $array, callable $callback): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $mapped = $callback($value, $key);

            if (! is_array($mapped)) {
                continue;
            }

            foreach ($mapped as $mapKey => $mapValue) {
                $result[$mapKey] = $mapValue;
            }
        }

        return $result;
    }

    /**
     * Filter items out of the array using the given callback.
     *
     * @param  array<mixed>  $array
     * @param  callable  $callback
     * @return array<mixed>
     */
    public static function reject(array $array, callable $callback): array
    {
        return static::where($array, function ($value, $key) use ($callback) {
            return ! $callback($value, $key);
        });
    }

    /**
     * Filter items where the value is not null.
     *
     * @param  array<mixed>  $array
     * @return array<mixed>
     */
    public static function whereNotNull(array $array): array
    {
        return static::where($array, function ($value) {
            return ! is_null($value);
        });
    }

    /**
     * Join all items using a string. The final value can use a different glue.
     *
     * @param  array<mixed>  $array
     * @param  string  $glue
     * @param  string  $finalGlue
     * @return string
     */
    public static function join(array $array, string $glue, string $finalGlue = ''): string
    {
        if ($finalGlue === '' || empty($array)) {
            return implode($glue, array_map(static function ($v) {
                return (string) $v;
            }, $array));
        }

        if (count($array) > 1) {
            return implode($glue, array_map(static function ($v) {
                return (string) $v;
            }, array_slice($array, 0, -1))) . $finalGlue . (string) end($array);
        }

        return (string) $array[0];
    }

    /**
     * Cross join the given arrays, returning all possible permutations.
     *
     * @param  array<mixed>  ...$arrays
     * @return array<mixed>
     */
    public static function crossJoin(...$arrays): array
    {
        $results = [[]];

        foreach ($arrays as $index => $array) {
            $append = [];

            foreach ($results as $product) {
                foreach ($array as $item) {
                    $product[$index] = $item;
                    $append[] = $product;
                }
            }

            $results = $append;
        }

        return $results;
    }

    /**
     * Key an associative array by a given key.
     *
     * @param  array<mixed>  $array
     * @param  string|callable  $keyBy
     * @return array<mixed>
     */
    public static function keyBy(array $array, $keyBy): array
    {
        $results = [];

        foreach ($array as $key => $item) {
            if (is_string($keyBy)) {
                $newKey = static::get((array) $item, $keyBy);
            } else {
                $newKey = $keyBy($item, $key);
            }

            $results[$newKey] = $item;
        }

        return $results;
    }
}
