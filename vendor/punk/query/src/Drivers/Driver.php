<?php

/**
 * Driver
 * PHP version 7.4
 *
 * @category Drivers
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Drivers;

use \Punk\Query\Connectors\ConnectorInterface;
use \Punk\Query\Connections\ConnectionInterface;
use \Punk\Query\Script\BuilderFactory;
use \Punk\Query\Script\Builder;
use \Punk\Query\Connectors\ConnectorFactory;

abstract class Driver implements DriverInterface {

    public function getConnector(): ConnectorInterface {
        return ConnectorFactory::resolveFactory($this);
    }

    public function getConnection($configuration): ConnectionInterface {
        return $this->getConnector()->getConnection($configuration);
    }

    public function getBuilder(ConnectionInterface $connection): Builder {
        return BuilderFactory::resolveFactory($connection);
    }

}
