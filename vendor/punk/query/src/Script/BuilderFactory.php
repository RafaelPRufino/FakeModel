<?php

/**
 * Builder Factory
 * PHP version 7.4
 *
 * @category Script
 * @package  Punk\Query\Script
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Script;

use \Punk\Query\Connections\ConnectionInterface; 

class BuilderFactory {

    /**
     * Retorna.
     *
     * @param  ConnectionInterface  $connection
     * @return string
     */
    public static function resolveFactory(ConnectionInterface $connection):Builder {
        $supportedDrivers = static::supportedDrivers();
        $abstract = $supportedDrivers[$connection->getDriver()->getName()];
        return new $abstract($connection, $connection->getDriver()->getLanguage());
    }

    public static function supportedDrivers(): array {
        return ['sqlite' => Builder::class, 'mysql' => MySqlBuilder::class];
    }

}
