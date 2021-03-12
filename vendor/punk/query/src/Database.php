<?php

/**
 * Database
 * PHP version 7.4
 *
 * @category Manager
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query;

use Punk\Query\Utils\Container;
use Punk\Query\Drivers\DriverFactory;
use \Punk\Query\Connections\ConnectionInterface;

class Database {

    protected Container $container;
    protected Drivers\DriverInterface $driver;
    protected Connectors\ConnectorInterface $connector;
    protected Connections\ConnectionInterface $connection;
    protected Array $configuration;

    public function __construct(Array $configuration) {
        $this->container = new Container();
        $this->container->bind('config::connection', $configuration);

        $this->configuration = $configuration;
        $this->driver = DriverFactory::resolveFactory($configuration);
        $this->connector = $this->driver->getConnector();

        $this->config();
    }

    public function config() {
        Capsule\Capsule::init($this);
    }

    /**
     * Get all of the support drivers.
     *
     * @return array
     */
    public function supportedDrivers(): Array {
        return ['sqlite', 'mysql'];
    }

    /**
     * Get a database connection instance.
     *
     * @return \Punk\Query\Connections\ConnectionInterface
     */
    public function connection(): ConnectionInterface {
        if (!isset($this->connection)) {
            $this->connection = $this->connection = $this->connector->getConnection($this->configuration);
        }
        return $this->connection;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->connection()->$method(...$parameters);
    }

}
