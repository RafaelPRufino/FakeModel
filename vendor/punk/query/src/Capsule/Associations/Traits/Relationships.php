<?php

namespace Punk\Query\Capsule\Associations\Traits;

use \Punk\Query\Capsule\Capsule;
use \Punk\Query\Capsule\Associations\BelongsTo;
use \Punk\Query\Capsule\Associations\BelongsToMany;
use \Punk\Query\Capsule\Associations\HasMany;
use \Punk\Query\Capsule\Associations\HasOne;
use \Punk\Query\Capsule\Associations\Relation;
use \Punk\Query\Utils as Suporte;

trait RelationShips {

    /**
     * variable relationships
     * type
     * */
    private static $relationships = [];

    /**
     * variable relationships
     * type
     * */
    private $resolvedRelations = [];

    /**
     * variable relationships
     * type
     * */
    private $loadedRelations = [];

    /**
     * Get and Set variable Relations
     * @return and @param type
     * */
    protected function getRelations() {
        $class = get_called_class();
        if (!Suporte\Arr::key_exists($class, static::$relationships)) {
            static::$relationships[$class] = array();
        }
        return static::$relationships[$class];
    }

    protected function setRelations($newRelations) {
        $class = get_called_class();
        if (!Suporte\Arr::key_exists($class, static::$relationships)) {
            static::$relationships[$class] = array();
        }
        static::$relationships[$class] = $newRelations;
    }

    /**
     * Get and Set variable Relations
     * @return and @param type
     * */
    protected function getResolvedRelation($relationKey = null): Relation {
        return $this->resolvedRelations[$relationKey] ?? null;
    }

    /**
     * Get and Set variable Relations
     * @return and @param type
     * */
    protected function getResolvedRelations() {
        return $this->resolvedRelations;
    }

    /**
     * Get and Set variable Relations
     * @return and @param type
     * */
    protected function getloadedRelations() {
        return $this->loadedRelations;
    }

    public static function belongsTo(string $relationName, string $model, string $foreignKey = null, string $onwerkey = null) {
        $class = get_called_class();
        $onwer = new $model();

        if (is_null($foreignKey)) {
            $foreignKey = $onwer->getModelKeyName();
        }

        if (is_null($onwerkey)) {
            $onwerkey = $onwer->getModelKeyName();
        }

        static::addRelation($class, $relationName, [BelongsTo::class => [new $model(), $relationName, $onwerkey, $foreignKey]]);
    }

    public static function belongsToMany(string $relationName, string $model, string $table = null, string $sourcekey = null, string $destinyKey = null) {
        $class = get_called_class();
        $destiny = new $model();

        if (is_null($table)) {
            $table = $destiny->getTable() . '_' . (new $class())->getTable();
        }

        if (is_null($destinyKey)) {
            $destinyKey = $destiny->getModelKeyName();
        }

        if (is_null($sourcekey)) {
            $sourcekey = (new $class())->getModelKeyName();
        }
       
        static::addRelation($class, $relationName, [BelongsToMany::class => [new $model(), $table, $relationName, $sourcekey, $destinyKey]]);
    }

    public static function hasMany(string $relationName, string $model, string $foreignKey = null, string $onwerkey = null) {
        $class = get_called_class();
        $onwer = new $class();

        if (is_null($foreignKey)) {
            $foreignKey = $onwer->getModelKeyName();
        }

        if (is_null($onwerkey)) {
            $onwerkey = $onwer->getModelKeyName();
        }

        static::addRelation($class, $relationName, [HasMany::class => [new $model(), $relationName, $onwerkey, $foreignKey]]);
    }

    public static function hasOne(string $relationName, string $model, string $foreignKey = null, string $onwerkey = null) {
        $class = get_called_class();
        $onwer = new $class();

        if (is_null($foreignKey)) {
            $foreignKey = $onwer->getModelKeyName();
        }

        if (is_null($onwerkey)) {
            $onwerkey = $onwer->getModelKeyName();
        }

        static::addRelation($class, $relationName, [HasOne::class => [new $model(), $relationName, $onwerkey, $foreignKey]]);
    }

    public function resolveRelations() {
        $relations = $this->getRelations();
        foreach ($relations as $relationKey => $relation) {
            $type = Suporte\Arr::key_first($relation);
            $configs = array_values($relation[$type]);

            array_unshift($configs, $this->newBuilder()->clone(), $this);

            $this->resolvedRelations[$relationKey] = new $type(...$configs);
        }
    }

    protected function hasRelation($relationKey): bool {
        return Suporte\Arr::key_exists($relationKey, $this->resolvedRelations);
    }

    protected function relationLoaded($relationKey): bool {
        return Suporte\Arr::key_exists($relationKey, $this->loadedRelations);
    }

    protected function saveRelation($relationKey, $models) {
        $relation = $this->getResolvedRelation($relationKey);
        $relation->save($models);
    }

    private static function addRelation(string $class, string $name, array $relationConfig) {
        if (!Suporte\Arr::key_exists($class, static::$relationships)) {
            static::$relationships[$class] = array();
        }
        static::$relationships[$class][$name] = $relationConfig;
    }

    private function getRelationValues($relationKey) {
        $relation = $this->getResolvedRelation($relationKey);
        $this->loadedRelations[$relationKey] = $relation;

        if ($this->isLoad($relation)) {
            return $relation->getRelationValue();
        }

        return $relation->getDefault();
    }

    private function setRelationValues($relationKey, &$values) {
        $relation = $this->getResolvedRelation($relationKey);
        return $relation->setRelationValue($values);
    }

    private function isLoad(Relation $relation) {
        $parent = $this->getParent();
        if (is_null($parent)) {
            return true;
        }

        $classModel = $relation->getModel();
        $model = new $classModel();

        if ($parent->is($model)) {
            return false;
        }

        if (!is_null($parent->getParent())) {
            $parentOfParent = $parent->getParent();
            return !$this->is($parentOfParent);
        }

        return true;
    }

}
