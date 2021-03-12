<?php

namespace Punk\Query\Capsule\Associations;

use \Punk\Query\Utils\Str;
use \Punk\Query\Utils\Arr;
use \Punk\Query\Script\Builder as QueryBuilder;
use Punk\Query\Capsule\Builder as CapsuleBuilder;
use Punk\Query\Capsule\Capsule as Model;
use \Punk\Query\Utils as Suporte;

abstract class Relation {

    /**
     * variable stringRelationName
     * type String
     * */
    private $stringRelationName;

    /**
     * Get and Set variable stringRelationName
     * @return and @param type String
     * */
    public function getRelationName() {
        return $this->stringRelationName;
    }

    protected function setRelationName($newStringRelationName) {
        $this->stringRelationName = $newStringRelationName;
    }

    /**
     * variable builderQuery
     * type Builder
     * */
    private $builderQuery;

    /**
     * Get and Set variable builderQuery
     * @return and @param type Builder
     * */
    protected function getQuery() {
        return $this->builderQuery;
    }

    protected function setQuery($newBuilderQuery) {
        $this->builderQuery = $newBuilderQuery;
    }

    public function __construct($query, $relationName) {
        $this->setQuery($query);
        $this->setRelationName($relationName);
    }

    abstract protected function newModel($attributes);

    abstract protected function setRelation($model);

    abstract function getModel();

    abstract function getDefault();

    abstract function getRelationValue();

    abstract function setRelationValue($models);

    abstract function save($models);
}
