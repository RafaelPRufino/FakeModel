<?php

namespace Punk\Query\Fake\Capsule\Associations;

use \Punk\Query\Utils as Suport;

class HasOne extends Relation {

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
     * @return and @param type String
     * */
    protected function getForeignkey() {
        return $this->stringForeignkey;
    }

    protected function setForeignkey($newStringForeignkey) {
        $this->stringForeignkey = $newStringForeignkey;
    }

    /**
     * variable capsuleModel
     * type Capsule
     * */
    private $capsuleModel;

    /**
     * Get and Set variable capsuleModel
     * @return and @param type Capsule
     * */
    function getModel() {
        return $this->capsuleModel;
    }

    protected function setModel($newCapsuleModel) {
        $this->capsuleModel = $newCapsuleModel;
    }

    public function __construct($query, $parent, $model, $relationName, $primaryKey, $foreignKey) {
        $this->setParent($parent);
        $this->setModel($model);
        $this->setPrimarykey($primaryKey);
        $this->setForeignkey($foreignKey);
        parent::__construct($query, $relationName, $parent);
    }

    protected function newModel($attributes) {
        $class = get_class($this->getModel());
        return new $class($attributes);
    }

    public function getRelationValue() {
        $query = $this->getQuery()->setModel($this->newModel([]));
        return $this->setRelation($query->where([$this->getForeignkey(), ' = ', $this->getParent()->{$this->getPrimarykey()}])->get());
    }

    public function setRelationValue($models) {
        return Suport\Arr::first($this->setRelation($models));
    }

    protected function setRelation($models) {
        $parent = $this->getParent();
        return Suport\Arr::first(Suport\Arr::map($models, function(&$model)use($parent) {
                            if (is_array($model)) {
                                $model = $this->newModel($model);
                            }
                            $model->setParent($parent);
                            $model->{$this->getForeignkey()} = $parent->{$this->getPrimarykey()};
                            return $model;
                        }));
    }

    public function save($models) {
        $parent = $this->getParent();

        if (!$parent->exists()) {
            $parent->save();
        }

        $preparedModels = Suport\Arr::map($models, function(&$model)use($parent) {
                    if (is_array($model)) {
                        $model = $this->newModel($model);
                    }
                    return $model;
                });

        $relatedModels = $this->setRelation($preparedModels);
        return Suport\Arr::first(Suport\Arr::map($relatedModels, function(&$model) {
                            if (!$model->exists()) {
                                $model->save();
                            }
                        }));
    }

    public function getDefault() {
        return $this->newModel([]);
    }

}
