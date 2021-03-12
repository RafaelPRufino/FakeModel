<?php

/**
 * Connection
 * PHP version 7.4
 *
 * @category Connections
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Connections;

use PDO;
use \Punk\Query\Drivers\DriverInterface;
use \Punk\Query\Script\BuilderInterface;

class Connection implements ConnectionInterface {

    /**
     * variable pdoPDO
     * type PDO
     * */
    private PDO $pdoPDO;

    /**
     * Get and Set variable pdoPDO
     * @return and @param type PDO
     * */
    protected function getPDO(): PDO {
        return $this->pdoPDO;
    }

    protected function setPDO(PDO $newPdoPDO): void {
        $this->pdoPDO = $newPdoPDO;
    }

    /**
     * variable $driver
     * type DriverInterface
     * */
    private DriverInterface $driver;

    /**
     * Get and Set variable DriverInterface
     * @return and @param DriverInterface
     * */
    public function getDriver(): DriverInterface {
        return $this->driver;
    }

    protected function setDriver(DriverInterface $newDriver): void {
        $this->driver = $newDriver;
    }

    public function __construct(DriverInterface $driver, PDO $newPdoPDO) {
        $this->setPDO($newPdoPDO);
        $this->setDriver($driver);
    }

    /**
     * Informar aos $statement os valores da query
     * @param $statement PDOStatement
     * @param Array $bindings  Array of Values
     * @return void
     * */
    public function bindValues(&$statement, $bindings = []): void {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                    is_string($key) ? $key : $key + 1,
                    $value,
                    is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR
            );
        }
    }

    /**
     * Retorna se há uma connexão com banco de dados 
     * @return bool
     * */
    public function connected(): bool {
        return $this->pdoPDO ? true : false;
    }

    /**
     * Retorna o ultimo Identificador Autoincremente Inserido
     * @param $name string 
     * @return int
     * */
    public function lastInsertId($name = null): int {
        if (is_null($name)) {
            return $this->getPDO()->lastInsertId();
        }
        return $this->getPDO()->lastInsertId($name);
    }

    /**
     * retorna uma PDOStatement
     * @param $query query string
     * @param $bindings Array of Values
     * @return PDOStatement
     * */
    public function statement($query, $bindings = []): \PDOStatement {
        $statement = $this->getPDO()->prepare($query);
        $this->bindValues($statement, $bindings);
        $statement->execute();
        return $statement;
    }

    /**
     * Retorna um conjunto de dados selecionados pelo select
     * @param string $query Query String
     * @param Array  $bindings Array of Values 
     * @return mixed
     * */
    public function select($query, $bindings = []) {
        return $this->execute($query, $bindings, function ($statement) {
                    return $statement->fetchAll(\PDO::FETCH_ASSOC);
                });
    }

    /**
     * Executa uma query
     * @param $query query string
     * @param $bindings Array of Values
     * @return midex
     * */
    public function execute(string $query, array $bindings, \Closure $callback) {
        return $callback($this->statement($query, $bindings), $this);
    }

    /**
     * Retorna um ScriptBuilder
     * @param string $table
     * @param string $as
     * @return \Punk\Query\Script\Builder
     */
    public function from($table, $as = null) {
        return $this->query()->from($table, $as);
    }
    
     /**
     * Retorna um ScriptBuilder
     * @param string $table
     * @param string $as
     * @return \Punk\Query\Script\Builder
     */
    public function fromSub($table, $as) {
        return $this->query()->fromSub($table, $as);
    }

    /**
     * Retorna um ScriptBuilder
     *
     * @return \Punk\Query\Script\Builder
     */
    public function query() {
        return $this->getDriver()->getBuilder($this);
    }

}
