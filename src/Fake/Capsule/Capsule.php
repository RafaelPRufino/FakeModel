<?php

namespace Punk\Query\Fake\Capsule;

use \Punk\Query\Utils\Str;
use \Punk\Query\Utils\Arr;
use \Punk\Query\Database;
use \Punk\Query\Script\Builder as QueryBuilder;
use \Punk\Query\Connections\ConnectionInterface;
use \Punk\Query\Utils as Suporte;
use \JsonSerializable;

abstract class Capsule implements JsonSerializable {

    use \Punk\Query\Fake\Refactor\Traits\Attributes;
    use \Punk\Query\Fake\Refactor\Traits\Persistence;
    use \Punk\Query\Fake\Capsule\Associations\Traits\Relationships;

    /**
     * Manager DataBase
     * @return Punk\Query\Database
     * */
    protected static Database $instance;

    /**
     * PrimayKey do Model
     * @return string
     * */
    protected $primarykey = null;
    
     /**
     * PrimayKey do Model
     * @return string
     * */
    protected $primarykey_type = int;

    /**
     * Tabela fonte de dados do Model
     * @return string
     * */
    protected $table = null;

    /**
     * Determina a PrimaryKey é autoincremente
     * @return string
     * */
    protected bool $incrementing = true;

    /**
     * Inicializa o Capsule
     * @return void
     * */
    public static function init(Database $instance) {
        static::$instance = $instance;
    }

    public function __construct($attributes = []) {
        $this->config($attributes);
    }

    /**
     * Realiza as configurações iniciais no Model
     * @return void
     * */
    protected function config($attributes): void {
        $this->classname = get_called_class();

        if (empty($this->table)) {
            $this->table = Str::lower($this->classname) . 's';
        }

        if (empty($this->primarykey)) {
            $this->primarykey = Str::lower($this->table) . '_id';
        }

        $this->loadColumns();

        $this->loadModel($attributes);

        $this->resolveRelations();
    }

    /**
     * Inicializa modelo com dados
     * @param  $attributes array de Atribultos
     */
    protected function loadModel($attributes): void {
        $this->fill($attributes);
        $this->exists = !is_null($this->getKey()) ? strlen((string) $this->getKey()) > 0 : false;
        
        if ($this->exists) {
            $this->clearChanges();
        }
    }

    /**
     * Parent Model
     * @return Capsule
     * */
    protected $parent = null;

    /**
     * Get and Set variable capsuleParent
     * @return Capsule parent que carregou o modelo
     * */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Informa o parent do Model
     * @param Capsule parent que carregou o modelo
     * */
    public function setParent($newCapsuleParent): void {
        $this->parent = $newCapsuleParent;
    }

    /**
     * Nome da Classe model
     * @return string
     * */
    protected string $classname = '';

    /**
     * Nome base da class Modelo
     *
     * @return string
     */
    public static function getBaseClassName() {
        return __CLASS__;
    }

    /**
     * Colunas que o Modelo repesenta  no banco de dados
     *
     * @return array Colunas SQL
     */
    protected static array $columns = array();

    /**
     * Colunas que o Modelo repesenta no banco de dados
     *
     * @return array Colunas SQL
     */
    protected function getColumns() {
        return static::$columns[$this->classname] ?? [];
    }

    /**
     * Carrega as colunas da tabela que representam o Modelo
     */
    protected function loadColumns(): void {
        if (!array_key_exists($this->classname, static::$columns)) {
            static::$columns[$this->classname] = [];

            $punk = static::$instance->fromSub("select -1 'punk_id'", 'punk');
            $punk->leftJoin($this->table, function ($join) {
                $join->on(['punk.punk_id', $this->table . '.' . $this->getModelKeyName()]);
            });
            $punk->select([$this->table . '.*']);

            $keys = array_keys($punk->statement()->fetch(\PDO::FETCH_ASSOC));

            foreach ($keys as $key) {
                static::$columns[$this->classname] [] = $key;
            }
        }
    }

    /**
     * Filtra os atributos que pertencem a tabela 
     * @param  $attributes array de Atribultos
     * @return array Atributos
     */
    protected function filterColumns($attributes) {
        $columns = $this->getColumns();
        return Arr::queryBy($attributes, function($value, $key)use($columns) {
                    return Suporte\Arr::in_array($key, $columns) && $key != $this->getModelKeyName();
                });
    }

    /**
     * Model Existe
     * @return bool
     * */
    protected bool $exists = false;

    /**
     * Informa se o modelo existe                
     *
     * @return bool Modelo existe no banco de dados
     */
    public function exists(): bool {
        return $this->exists;
    }

    /**
     * Get a database connection instance.
     *
     * @return \Punk\Query\Connections\ConnectionInterface
     */
    public function getConnection(): ConnectionInterface {
        return static::$instance->connection();
    }

    /**
     * Get a database builder instance.
     *
     * @return \Punk\Query\Script\Builder
     */
    public function getQueryBuilder(): QueryBuilder {
        return $this->getConnection()->query();
    }

    /**
     * Get a database builder instance.
     *
     * @return \Punk\Query\Capsule\Builder
     */
    public function newBuilder(): Builder {
        return (new Builder($this->getQueryBuilder()))->setModel($this);
    }

    /**
     * Retorna Nome da tabela do modelo
     *
     * @return string
     */
    public function getModelName() {
        return $this->table;
    }

    /**
     * Retorna o nome chave primaria
     *
     * @return string
     */
    public function getModelKeyName() {
        return $this->primarykey;
    }

    /**
     * Retorna o valor da chave primária
     *
     * @return mixed
     */
    public function getKey() {
        $key = $this->getAttribute($this->getModelKeyName());        
        if(((int) $key)<= 0){
            return null;
        }
        return $key;
    }

    /**
     * Cria um novo Model
     *
     * @return mixed
     */
    public function newModel($attributes = []) {
        return new static($attributes);
    }

    /**
     * Cria um novo Model
     *
     * @return mixed
     */
    public function is($model) {
        return !is_null($model) &&
                $this->getModelKeyName() === $model->getModelKeyName() &&
                $this->getModelName() === $model->getModelName();
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key) {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value) {
        $this->setAttribute($key, $value);
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        if ($this->hasRelation($method)) {
            return $this->saveRelation($method, ...$parameters);
        }        
        return $this->newBuilder()->$method(...$parameters);
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters) {
        return (new static)->$method(...$parameters);
    }
}
