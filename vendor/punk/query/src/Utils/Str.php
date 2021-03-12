<?php

/**
 * Str
 * PHP version 7.4
 *
 * @category Utils
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Utils;

class Str {

    public static function isMatch(string $str, string $regex, &$return = ''): bool {
        return preg_match($regex, $str, $return );
    }

    public static function contains(string $searchTo, string $find): bool {
        return !(strpos($searchTo, $find) === false);
    } 

    public static function detect_by_Array(string $str, array $regex): bool {
        return preg_match($regex, $str);
    }

    public static function lower(string $str): string {
        return strtolower($str);
    }

}
