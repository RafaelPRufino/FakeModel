<?php

namespace Punk\Query\Fake\Capsule\Associations;

use \Punk\Query\Utils as Suport;
use \Punk\Query\Script\Builder as QueryBuilder;

class BelongsToMany extends Relation {

    /**
     * variable stringSourceKey
     * type String
     * */
    private $stringSourceKey;

    /**
     * Get and Set variable stringSourceKey
     * @return and @param type String
     * */
    protected function getSourceKey() {
        return $this->stringSourceKey;
    }

    protected function setSourceKey($newStringSourceKey) {
        $this->stringSourceKey = $newStringSourceKey;
    }

    /**
     * variable stringDestinyKey
     * type String
     * */
    private $stringDestinyKey;

    /**
     * Get and Set variable stringDestinyKey
     * @return String
     * */
    protected function getDestinyKey() {
        return $this->stringDestinyKey;
    }

    /**
     * Get and Set variable stringDestinyKey
     * @return and @param type String
     * */
    protected function setDestinyKey($newStringDestinyKey) {
        $this->stringDestinyKey = $newStringDestinyKey;
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
     * variable $table
     * type String
     * */
    private $table;

    /**
     * Get and Set variable $table
     * @return String
     * */
    protected function getTable() {
        return $this->table;
    }

    /**
     * Get and Set variable $table
     * @return and @param type String
     * */
    protected function setTable($newTable) {
        $this->table = $newTable;
    }

    public function __construct($query, $parent, $model, $table, $relationName, $sourcekey, $destinyKey) {
        $this->setParent($parent);
        $this->setModel(get_class($model));
        $this->setTable($table);
        $this->setSourceKey($sourcekey);
        $this->setDestinyKey($destinyKey);
     
        parent::__construct($query, $relationName, $parent);
    }

    protected function newModel($attributes): \Punk\Query\Fake\Capsule\Capsule {
        $class = $this->getModel();
        return new $class($attributes);
    }

    public function getRelationValue() {
        $model = $this->newModel([]);
        $query = $this->getQuery()->setModel($model);
        $parent = $this->getParent();

        $query->innerJoin($this->getTable(), function ($join)use ($model) {
            $join->on([$model->getTable() . '.' . $this->getDestinyKey(), $this->getTable() . '.' . $this->getDestinyKey()]);
        });

        $query->innerJoin($parent->getTable(), function ($join)use ($parent) {
            $sourceKeyValue = $parent->{$this->getSourceKey()} ?? null;
            $join->on([$parent->getTable() . '.' . $parent->getModelKeyName(), $this->getTable() . '.' . $this->getSourceKey()]);
            $join->where([$parent->getTable() . '.' . $parent->getModelKeyName(), ' = ', $sourceKeyValue]);
        });

        $query->select([$model->getTable() . '.*']);
        var_dump(   $query->getQuery()->toSQL());
        return $this->setRelation($query->get());
    }

    public function setRelationValue($models) {
        return $this->setRelation($models);
    }

    protected function setRelation($models) {
        if (is_null($models)) {
            return null;
        }

        $parent = $this->getParent();
        return Suport\Arr::map($models, function (&$model)use ($parent) {
                    if (is_array($model)) {
                        $model = $this->newModel($model);
                    }
                    $model->setParent($this->getParent());
                    $parent->{$this->getSourceKey()} = $model->{$this->getDestinyKey()};

                    return $model;
                });
    }

    public function save($models) {
        $parent = $this->getParent();
        return Suport\Arr::map($models, function (&$model)use ($parent) {
                    if (is_array($model)) {
                        $model = $this->newModel($model);
                    }

                    if (!$model->exists()) {
                        $model->save();
                    }

                    $this->setRelation($model);
                    

                    $parent = $this->getParent();
                    $parent->save();
                    
                    
                    $query = $this->getQuery()->getQuery()->from($this->getTable());
                    $attributes = [$this->getSourceKey() =>  $parent->getKey(),
                                   $this->getDestinyKey() =>$model->getKey()];
                    
                               var_dump($this->getDestinyKey())     ;
                    $query->insert($attributes);
                    
                    
                    return $model;
                });
    }

    public function getDefault() {
        return null;
    }

}
