<?php

/**
 * Connector
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
use \Punk\Query\Connections\ConnectionFactory;
use PDO;
use PDOException;

abstract class Connector {

    /**
     * variable driverinterfaceDriver
     * type DriverInterface
     * */
    private $driverinterfaceDriver;

    /**
     * Get variable DriverInterface
     * @return type DriverInterface
     * */
    public function getDriver(): DriverInterface {
        return $this->driverinterfaceDriver;
    }

    /**
     * retorna Connector
     * @param $driver DriverInterface
     * @return $this
     * */
    public function __construct(DriverInterface $driver) {
        $this->driverinterfaceDriver = $driver;
    }

    /**
     * retorna uma conexão PDO
     * @param $dsn string de conexão DSN
     * @param $username usuário de conexão
     * @param $password senha de conexão
     * @param $options configuração de conexão
     * @return PDO
     * */
    protected function createConnection(string $dsn, string $username = null, string $password = null, Array $options = null): \PDO {
        return new PDO($dsn, $username, $password, $options);
    }

    public function getConnection(Array $configuration) {
        return ConnectionFactory::resolveFactory($this, $configuration);
    }

    protected function getDatabase($options) {
        [$database] = [
            $options['database'] ?? null
        ];
        return $database;
    }
}
