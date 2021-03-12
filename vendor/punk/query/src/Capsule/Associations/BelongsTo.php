<?php

namespace Punk\Query\Capsule\Associations;

use \Punk\Query\Utils as Suport;

class BelongsTo extends Relation {

    /**
     * variable stringPrimarykey
     * type String
     * */
    private $stringPrimarykey;

    /**
     * Get and Set variable stringPrimarykey
     * @return and @param type String
     * */
    protected function getPrimarykey() {
        return $this->stringPrimarykey;
    }

    protected function setPrimarykey($newStringPrimarykey) {
        $this->stringPrimarykey = $newStringPrimarykey;
    }

    /**
     * variable stringForeignkey
     * type String
     * */
    private $stringForeignkey;

    /**
     * Get and Set variable stringForeignkey
     * @return String
     * */
    protected function getForeignkey() {
        return $this->stringForeignkey;
    }

    /**
     * Get and Set variable stringForeignkey
     * @return and @param type String
     * */
    protected function setForeignkey($newStringForeignkey) {
        $this->stringForeignkey = $newStringForeignkey;
    }

    /**
     * variable capsuleModel
     * type Capsule
     * */
    private $classModel;

    /**
     * Get and Set variable capsuleModel
     * @return Capsule
     * */
    function getModel() {
        return $this->classModel;
    }

    protected function setModel($newCapsuleModel) {
        $this->classModel = $newCapsuleModel;
    }

    /**
     * variable capsuleParent
     * type Capsule
     * */
    private $capsuleParent;

    /**
     * Get and Set variable capsuleParent
     * @return Capsule
     * */
    protected function getParent() {
        return $this->capsuleParent;
    }

    /**
     * Get and Set variable capsuleParent
     * @return and @param type Capsule
     * */
    protected function setParent($newCapsuleParent) {
        $this->capsuleParent = $newCapsuleParent;
    }

    public function __construct($query, $parent, $model, $relationName, $primaryKey, $foreignKey) {
        $this->setParent($parent);
        $this->setModel(get_class($model));
        $this->setPrimarykey($primaryKey);
        $this->setForeignkey($foreignKey);
        parent::__construct($query, $relationName);
    }

    protected function newModel($attributes) {
        $class = $this->getModel();
        return new $class($attributes);
    }

    public function getRelationValue() {
        $model = $this->getModel();
        $query = $this->getQuery()->setModel(new $model);
        $fkey = $this->getParent()->{$this->getForeignkey()} ?? null;
        return $this->setRelation($query->where([$this->getPrimarykey(), ' = ', $fkey])->first());
    }

    public function setRelationValue($models) {
        $models->setParent($this->getParent());
        return $this->setRelation($models);
    }

    protected function setRelation($models) {
        if (is_null($models)) {
            return null;
        }

        $parent = $this->getParent();
        $parent->{$this->getForeignkey()} = $models->{$this->getPrimarykey()};
        $models->setParent($this->getParent());
        return $models;
    }

    function save($models) {
        if (is_array($models)) {
            $models = $this->newModel($models);
        }

        if (!$models->exists()) {
            $models->save();
        }

        $this->setRelation($models);
        $parent = $this->getParent();
        $parent->save();
    }

    public function getDefault() {
        return null;
    }

}
