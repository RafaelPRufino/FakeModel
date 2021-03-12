<?php

/**
 * ConnectorFactory
 * PHP version 7.4
 *
 * @category Connectors
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Connectors;

use \Punk\Query\Drivers\DriverInterface;

class ConnectorFactory {

    /**
     * @param  DriverInterface  $driver
     * @return string
     */
    public static function resolveFactory(DriverInterface $driver): ConnectorInterface {
        $supportedDrivers = static::supportedDrivers();
        $abstract = $supportedDrivers[$driver->getName()];
        return new $abstract($driver);
    }

    public static function supportedDrivers(): array {
        return ['sqlite' => SQLiteConnector::class, 'mysql' => MySqlConnector::class];
    }
}
