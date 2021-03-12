<?php

namespace Punk\Query\Fake;

use Punk\Query\Sql;
use Punk\Query\Capsule\Capsule;

class Model extends Capsule {

    public function __construct($attributes = []) {
        parent::__construct($attributes);
    }

    private static function callStaticMethods($method, $parameters) {
        return (new static)->newBuilder()->$method($parameters);
    }

    /**
     * Configura conexão com banco de dados.
     *
     * @param  array  $configuration
     * @return Void
     */
    public static function setConnection(Array $configuration): void {
        Sql::setConnection($configuration);
    }

    /**
     * Realiza um where Dinâmico
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Model
     */
    public static function where(Array $options) {
        return static::callStaticMethods('where', $options);
    }

    /**
     * Realiza um where Dinâmico
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public static function orWhere(Array $options) {
        return static::callStaticMethods('orWhere', $options);
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Model
     */
    public static function whereIsNull(Array $options) {
        return static::callStaticMethods('whereIsNull', $options);
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Model
     */
    public static function orWhereIsNull(Array $options) {
        return static::callStaticMethods('orWhereIsNull', $options);
    }

    /**
     * Realiza um where com a clausula Is Not Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Model
     */
    public static function whereIsNotNull(Array $options) {
        return static::callStaticMethods('whereIsNotNull', $options);
    }

    /**
     * Realiza um where com a clausula Is Not Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Model
     */
    public static function orWhereIsNotNull(Array $options) {
        return static::callStaticMethods('orWhereIsNotNull', $options);
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return  \Punk\Query\Fake\Model
     */
    public static function whereColumns(Array $options) {
        return static::callStaticMethods('whereColumns', $options);
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return  \Punk\Query\Fake\Model
     */
    public static function orWhereColumns(Array $options) {
        return static::callStaticMethods('orWhereColumns', $options);
    }

    /**
     * Realiza um where com a clausula In
     * @param Array $options configuração do where
     * @return  \Punk\Query\Fake\Model
     */
    public static function whereIn(Array $options) {
        return static::callStaticMethods('whereIn', $options);
    }

    /**
     * Realiza um where com a clausula In
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Model
     */
    public static function orWhereIn(Array $options) {
        return static::callStaticMethods('orWhereIn', $options);
    }

    /**
     * Realiza um where com a clausula Not In
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Model
     */
    public static function whereNotIn(Array $options) {
        return static::callStaticMethods('whereNotIn', $options);
    }

    /**
     * Realiza um where com a clausula Not In
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Model
     */
    public static function orWhereNotIn(Array $options) {
        return static::callStaticMethods('orWhereNotIn', $options);
    }

    /**
     * Busca model pelo Id
     * @param string|mixed $id id de identificação
     * @return \Punk\Query\Fake\Model
     */
    public static function find(string $id) {
        return static::callStaticMethods('find', $id);
    }

    /**
     * Retorna todas os modelos do banco
     * @return Array \Punk\Query\Fake\Model
     */
    public static function all() {
        return static::callStaticMethods('all', []);
    }

    /**
     * First Object 
     * @return Array \Punk\Query\Fake\Model|null
     */
    public static function first() {
        return static::callStaticMethods('first', []);
    }

    /**
     * First or Default Object 
     * @return Array \Punk\Query\Fake\Model 
     */
    public static function firstOrDefault() {
        return static::callStaticMethods('firstOrDefault', []);
    }

    /**
     * Get all Object
     * @return Array of \Punk\Query\Fake\Model 
     */
    public static function page(int $numberOfPage, int $quantityOfPage) {
        return (new static)->newBuilder()->page($numberOfPage, $quantityOfPage);
    }
}
