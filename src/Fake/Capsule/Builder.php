<?php

namespace Punk\Query\Fake\Capsule;

use \Punk\Query\Utils as Suporte;
use \Punk\Query\Script\Builder as QueryBuilder;
use \Iterator;
use \JsonSerializable;

class Builder implements Iterator, JsonSerializable {

    use \Punk\Query\Fake\Refactor\Traits\Collection;

    protected $query;

    /**
     * variable capsuleModel
     * type Capsule
     * */
    private $capsuleModel;

    /**
     * Get and Set variable capsuleModel
     * @return and @param type Capsule
     * */
    public function getModel(): Capsule {
        return $this->capsuleModel;
    }

    public function setModel(Capsule $newCapsuleModel) {
        $this->capsuleModel = $newCapsuleModel;
        $this->getQuery()->from($this->getModel()->getModelName());
        return $this;
    }

    /**
     * variable querybuilderQuery
     * type QueryBuilder
     * */
    private $querybuilderQuery;

    /**
     * Get and Set variable querybuilderQuery
     * @return and @param type QueryBuilder
     * */
    public function getQuery(): QueryBuilder {
        return $this->querybuilderQuery;
    }

    public function setQuery(QueryBuilder $newQuerybuilderQuery) {
        $this->querybuilderQuery = $newQuerybuilderQuery;
        return $this;
    }

    public function __construct(QueryBuilder $query) {
        $this->setQuery($query);
    }

    /**
     * Get and Set variable querybuilderQuery
     * @return and @param type QueryBuilder
     * */
    protected function newModel($attributes) {
        return $this->getModel()->newModel($attributes ?? array());
    }

    /**
     * Realiza insert dos atributos no banco de dados
     * @param \Punk\Query\Fake\Capsule\Builder $attributes bindings
     * @return bool
     * */
    public function insert(array $attributes): bool {
        return $this->getQuery()->insert($attributes);
    }

    /**
     * Realiza insert dos atributos no banco de dados
     * @param \Punk\Query\Fake\Capsule\Builder $attributes bindings
     * @param string $keyId primary key
     * @return mixed
     * */
    public function insertGetId(array $attributes, $keyId = null) {
        return $this->getQuery()->insertGetId($attributes, $keyId);
    }

    /**
     * Realiza atualização dos atributos no banco de dados
     * @param \Punk\Query\Script\Builder $attributes bindings
     * @return bool
     * */
    public function update(array $attributes): bool {
        return $this->getQuery()->update($attributes);
    }

    /**
     * Realiza um where Dinâmico
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function where(Array $options = []): self {
        $this->getQuery()->where($options);
        return $this;
    }

    /**
     * Realiza um where Dinâmico
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhere(Array $options = []): self {
        $this->getQuery()->orWhere($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function whereIsNull(Array $options = []): self {
        $this->getQuery()->whereIsNull($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhereIsNull(Array $options = []): self {
        $this->getQuery()->orWhereIsNull($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Not Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function whereIsNotNull(Array $options = []): self {
        $this->getQuery()->whereIsNotNull($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Not Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhereIsNotNull(Array $options = []): self {
        $this->getQuery()->orWhereIsNotNull($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function whereColumns(Array $options = []): self {
        $this->getQuery()->whereColumns($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhereColumns(Array $options = []): self {
        $this->getQuery()->orWhereColumns($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula In
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function whereIn(Array $options = []): self {
        $this->getQuery()->whereIn($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula In
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Capsule\Builder
     */
    public function orWhereIn(Array $options = []): self {
        $this->getQuery()->orWhereIn($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula Not In
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Capsule\Builder
     */
    public function whereNotIn(Array $options = []): self {
        $this->getQuery()->whereNotIn($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula Not In
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Capsule\Builder
     */
    public function orWhereNotIn(Array $options = []): self {
        $this->getQuery()->orWhereNotIn($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula like
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Capsule\Builder
     */
    public function whereLike(Array $options = []): self {
        $this->getQuery()->whereLike($options);
        return $this;
    }

    /**
     * Realiza um where com a clausula like
     * @param Array $options configuração do where
     * @return \Punk\Query\Fake\Capsule\Builder
     */
    public function orWhereLike(Array $options = []): self {
        $this->getQuery()->orWhereLike($options);
        return $this;
    }

    /**
     * Informa que será efetuado um limit no Select
     * @return \Punk\Query\Fake\Capsule\Builder
     */
    public function limit($limit = -1): self {
        $this->getQuery()->limit($limit);
        return $this;
    }

    /**
     * Find Object ById
     * @param int|string $id configuração do where
     * @return Object
     */
    public function find($id) {
        $finded = $this->getQuery()->forQuery()->where([$this->getModel()->getModelKeyName(), ' = ', $id]);
        return $this->firstElement($finded);
    }

    /**
     * Find Object ById
     * @param int|string $id configuração do where
     * @return Object
     */
    public function findOrDefault($id) {
        return $this->find($id) ?? $this->newModel([]);
    }

    /**
     * First Object 
     * @return Object
     */
    public function first() {
        $finded = $this->getQuery();
        return $this->firstElement($finded);
    }

    /**
     * First or Default Object 
     * @return Object
     */
    public function firstOrDefault() {
        $finded = $this->getQuery();
        return $this->firstElement($finded) ?? $this->newModel([]);
    }

    /**
     * Get all Object
     * @return array of object
     */
    public function all() {
        return $this->allElements($this->getQuery()->forQuery());
    }

    /**
     * Get cuurent Query Object
     * @return \Punk\Query\Fake\Capsule\Capsule Object collection
     * */
    public function get() {
        return $this->allElements($this->getQuery());
    }

    /**
     * Get all Object
     * @return array of object
     */
    public function page(int $numberOfPage, int $quantityOfPage) {
        return $this->allElements($this->getQuery()->page($quantityOfPage, $numberOfPage));
    }

    protected function firstElement($query) {
        $values = $query->runSelect();
        return $values ? $this->newModel(array_shift($values)) : null;
    }

    protected function allElements($query) {
        $values = $query->runSelect();
        return $values ? Suporte\Arr::map($values, function ($attributes) {
                    return $this->newModel($attributes);
                }) : [];
    }

    protected function statement() {
        return $this->getQuery()->clone()->statement();
    }

    /**
     * Faz um clone do Builder
     * @return \Punk\Query\Fake\Capsule\Builder clone de \Punk\Query\Fake\Capsule\Builder
     * */
    public function clone(): self {
        return clone $this;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->getQuery()->$method(...$parameters);
    }

    public function jsonSerialize() {
        return $this->getQuery()->runSelect();
    }
}
