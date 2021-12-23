<?php

/**
 * Persistence
 * PHP version 7.4
 *
 * @category Utils
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Fake\Refactor\Traits;

use \Punk\Query\Utils as Suport;

trait Persistence {

    public function save() {
        $this->performSave();
    }

    public function performSave($loop = true) {
        $model = $this;
        $attributes = $model->exists() ? $model->getAttributesChanges() : $model->getAttributes();
        
       
        if ($model->exists()) {
            $columns = $this->filterColumns($attributes);

            $model->where([$model->getModelKeyName(), $model->getAttribute($model->getModelKeyName())])->update($columns);
        } else if ($model->incrementing) {
            $columns = $this->filterColumns($attributes);
  
            $id = $model->insertGetId($columns, $model->getModelKeyName());
            if ($id > 0) {
                $find = $this->classname::find($id);
                $this->config($find->attributes);
            }
        } else if (!$model->incrementing) {
            $id = $model->getAttribute($model->getModelKeyName());

            $columns = $this->filterColumns($attributes);

            $model->insert($columns, $model->getModelKeyName());
            if (!empty($id)) {
                $find = $this->classname::find($id);
                $this->config($find->attributes);
            }
        }

        $this->performSaveRelations($attributes);

        return $this;
    }

    public function performSaveRelations($attributes) {
        $relations = $this->getloadedRelations();

        foreach ($relations as $relation) {
            if (Suport\Arr::key_exists($relation->getRelationName(), $attributes)) {
                $attribute = $attributes[$relation->getRelationName()];
                $this->performSaveInRelation(Suport\Arr::toArray($attribute));
            }
        }
    }

    protected function performSaveInRelation($models) {
        foreach ($models as $model) {
            if (method_exists($model, 'performSave')) {
                $model->performSave();
            }
        }
    }

}
