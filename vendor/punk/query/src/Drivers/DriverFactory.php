<?php

/**
 * Driver Factory
 * PHP version 7.4
 *
 * @category Drivers
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Drivers;

class DriverFactory {

    /**
     * Get the database client command to run.
     *
     * @param  array  $connection
     * @return string
     */
    public static function resolveFactory(array $connection): DriverInterface {
        $supportedDrivers = static::supportedDrivers();
        $abstract = $supportedDrivers[$connection['driver']];
        return new $abstract();
    }

    public static function supportedDrivers(): array {
        return ['sqlite' => SQLiteDriver::class, 'mysql' => MySqlDriver::class];
    }
}
