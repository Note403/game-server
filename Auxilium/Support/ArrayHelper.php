<?php

namespace Auxilium\Support;

class ArrayHelper
{
    public static function get(array $array, string|int $key)
    {
        if (is_integer($key))
            return $array[$key] ?? null;

        if (self::isDotNotation($key)) {
            foreach (self::explodeDotNotation($key) as $part_key) {
                $array = $array[$part_key] ?? null;

                if ($array == null)
                    return $array;
            }

            return $array;
        }

        return $array[$key] ?? null;
    }

    public static function pull(array &$array, string|int $key)
    {
        $return_value = $array[$key];
        unset($array[$key]);
        return $return_value;
    }

    public static function combine(array $arrays): array
    {
        $combined_array = array();

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                $combined_array[$key] = $value;
            }
        }

        return $combined_array;
    }

    public static function first(array &$array)
    {
        return $array[0];
    }

    public static function last(array &$array)
    {
        return $array[count($array) - 1];
    }

    private static function isDotNotation(string $key): bool
    {
        return str_contains('.', $key);
    }

    private static function explodeDotNotation(string $key): array
    {
        return explode('.', $key);
    }
}