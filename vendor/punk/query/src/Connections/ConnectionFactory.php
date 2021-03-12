<?php

/**
 * Connection Factory
 * PHP version 7.4
 *
 * @category Connections
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Connections;

use \Punk\Query\Connectors\ConnectorInterface;

class ConnectionFactory {

    /**
     * Get the database client command to run.
     *
     * @param  \Punk\Query\Connectors\ConnectorInterface  $connector
     * @param  array  $options
     * @return \Punk\Query\Connections\ConnectionInterface
     */
    public static function resolveFactory(ConnectorInterface $connector, array $options): ConnectionInterface {
        $supportedDrivers = static::supportedDrivers();
        $abstract = $supportedDrivers[$connector->getDriver()->getName()];
        return new $abstract($connector->getDriver(), $connector->connect($options));
    }

    public static function supportedDrivers(): array {
        return ['sqlite' => SQLiteConnection::class, 'mysql' => MySqlConnection::class];
    }
}
