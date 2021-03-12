<?php

/**
 * DriverInterface
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
use \Punk\Query\Script\Languages\Language;
use \Punk\Query\Script\Builder;

interface DriverInterface {

    /**
     * Get Driver Name
     * @return type String
     * */
    function getName(): string;

    /**
     * Get Connector DataBase
     * @return ConnectorInterface
     * */
    function getConnector(): ConnectorInterface;

    /**
     * Get Connection DataBase
     * @return ConnectionInterface
     * */
    function getConnection($configuration): ConnectionInterface;

    /**
     * Get Language DataBase
     * @return \Punk\Query\Script\Languages\Language
     * */
    function getLanguage(): Language;

    /**
     * Get Builder DataBase
     * @return \Punk\Query\Script\BuilderInterface
     * */
    function getBuilder(ConnectionInterface $connection): Builder;
}
