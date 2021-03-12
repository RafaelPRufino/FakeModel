<?php

/**
 *
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
use PDO;

interface ConnectorInterface {
    /*
     * @name connect
     * @params $options:Array
     * cria conexão com banco e retorna um PDO     * 
     */

    public function connect(Array $options): PDO;

    /**
     * Get DriverInterface
     * @return type DriverInterface
     * */
    public function getDriver(): DriverInterface;
}
