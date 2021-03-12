<?php

/**
 * MySql Connector
 * PHP version 7.4
 *
 * @category Connectors
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Connectors;

class MySqlConnector extends Connector implements ConnectorInterface {

    public function connect(array $options): \PDO {
        [$username, $password] = [
            $options['username'] ?? null,
            $options['password'] ?? null
        ];

        $dsn = $this->getDsn($options);
        $database = $this->getDatabase($options);
        $pdo = $this->createConnection($dsn, $username, $password, $options);      
        
        if(!empty($database)){
            $pdo->exec("use `{$database}`;");
        }  
        
        return $pdo;
    }

    private function getDsn($options) {
        [$database, $driver, $port] = [
            $options['database'] ?? null,
            $options['driver'] ?? $this->getDriver()->getName(),
            $options['port'] ?? 0,
        ];
        $dsn = "%s:";
        $configuarion = [$driver];

        if ($port > 0) {
            $dsn = $dsn . "port=%s;";
            $configuarion[] = $port;
        }

        if (!empty($database)) {
            $dsn = $dsn . "dbname=%s;";
            $configuarion[] = $database;
        }

        return call_user_func_array('sprintf', [$dsn, ...$configuarion]);
    }
}
