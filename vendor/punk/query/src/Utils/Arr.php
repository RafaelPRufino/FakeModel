<?php

/**
 * Arr
 * PHP version 7.4
 *
 * @category Utils
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Utils;

class Arr {

    public static function key_first(Array $array) {
        return array_key_first($array);
    }

    public static function key_exists($key, Array $array) {
        return array_key_exists($key, $array);
    }

    public static function in_array($value, Array $array) {
        return in_array($value, $array);
    }

    public static function array_is_association(Array $array): bool {
        return static::is_association(static::key_first($array));
    }

    public static function is_association($key): bool {
        return !is_int($key);
    }

    public static function range(int $start = 1, int $end = 10): array {
        return array_keys(array_fill($start, $end, "page"));
    }

    public static function map($array, $callback): Array {
        $index = -1;
        $first = true;
        $map = [];

        foreach (is_array($array) ? $array : [$array] as $key => $value) {
            $index = $index + 1;
            $first = $index <= 0 ? true : false;
            $map[$key] = $callback($value, $key, $index);
        }

        return $map;
    }

    public static function findByIndex($array, int $find_index) {
        $index = -1;
        foreach (is_array($array) ? $array : [$array] as $key => $value) {
            $index = $index + 1;
            if ($find_index === $index) {
                return $value;
            }
        }
        return null;
    }

    public static function combineArray($source, $destiny) {
        $response = array();

        $value1 = self::toArray($source);
        $value2 = self::toArray($destiny);

        foreach ($value1 as $key => $value) {
            if (!static::is_association($key)) {
                $response[] = $value;
            } else {
                $response[$key] = $value;
            }
        }

        foreach ($value2 as $key => $value) {
            if (!static::is_association($key)) {
                $response[] = $value;
            } else {
                $response[$key] = $value;
            }
        }

        return $response;
    }

    public static function pushArray($source, $value) {
        $response = array();

        $value1 = self::toArray($source);

        foreach ($value1 as $value1_to) {
            $response [] = $value1_to;
        }

        $response [] = $value;

        return $response;
    }

    public static function toArray($value): Array {
        if (is_array($value) == false) {
            if ($value) {
                return array($value);
            } else {
                return array();
            }
        }
        return $value;
    }

    public static function queryBy($array, $callback): Array {
        $response = array();
        $index = 0;
        foreach (is_array($array) ? $array : [$array] as $key => $value) {
            $index = $index + 1;
            if ($callback($value, $key, $index)) {
                if (static::is_association($key)) {
                    $response[$key] = $value;
                } else {
                    $response[] = $value;
                }
            }
        }

        return $response;
    }

    public static function fillData(Array $data, &$target) {
        foreach ($data AS $key => $value) {
            $target->{$key} = $value;
        }
    }

    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  iterable  $array
     * @param  int  $depth
     * @return array
     */
    public static function flatten($array) {
        $merged = array();
        foreach ($array as $item) {
            if (!is_array($item)) {
                $merged[] = $item;
            } else {
                $merged = Arr::combineArray($merged, $item);
            }
        }
        return $merged;
    }

    public static function first($array) {
        $return = is_array($array) ? $array : [$array];
        return array_shift($return);
    }

}
