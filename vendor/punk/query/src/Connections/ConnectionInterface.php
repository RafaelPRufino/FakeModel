<?php

/**
 * Connection Interface
 * PHP version 7.4
 *
 * @category Connections
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Connections;

use \Punk\Query\Drivers\DriverInterface;

interface ConnectionInterface {

    /**
     * Get and Set variable DriverInterface
     * @return and @param DriverInterface
     * */
    public function getDriver(): DriverInterface;

    /**
     * Informar aos $statement os valores da query
     * @param $statement PDOStatement
     * @param Array $bindings  Array of Values
     * @return void
     * */
    function bindValues(PDOStatement &$statement, Array $bindings): void;

    /**
     * Retorna se há uma connexão com banco de dados 
     * @return bool
     * */
    function connected(): bool;

    /**
     * Executa uma query
     * @param $query query string
     * @param $bindings Array of Values
     * @return midex
     * */
    public function execute(string $query, array $bindings, \Closure $callback);

    /**
     * Retorna um ScriptBuilder
     * @param string $table
     * @param string $as
     * @return \Punk\Query\Script\Builder
     */
    public function from($table, $as = null);
    
    /**
     * Retorna um ScriptBuilder
     * @param string $table
     * @param string $as
     * @return \Punk\Query\Script\Builder
     */
    public function fromSub($table, $as);

    /**
     * Retorna o ultimo Identificador Autoincremente Inserido
     * @param $name string 
     * @return int
     * */
    function lastInsertId(string $name = null): int;

    /**
     * Retorna um ScriptBuilder
     * @return \Punk\Query\Script\Builder
     */
    public function query();

    /**
     * retorna uma PDOStatement
     * @param $query query string
     * @param $bindings Array of Values
     * @return PDOStatement
     * */
    function statement(string $query, Array $bindings): \PDOStatement;

    /**
     * Retorna um conjunto de dados selecionados pelo select
     * @param string $query Query String
     * @param Array  $bindings Array of Values 
     * @return mixed
     * */
    function select($query, $bindings = []);
}
