<?php

/**
 * SQLite Connector
 * PHP version 7.4
 *
 * @category Connectors
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Connectors;

class SQLiteConnector extends Connector implements ConnectorInterface {

    public function connect(array $options): \PDO {
        [$username, $password, $file] = [
            $options['username'] ?? null,
            $options['password'] ?? null,
            $options['file'] ?? null,
        ];

        $dsn = realpath($file);
    
        return $this->createConnection("sqlite:$dsn", $username, $password, $options);
    }
}
