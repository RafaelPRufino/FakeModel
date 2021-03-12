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

namespace Punk\Query\Utils\Traits;

use \Punk\Query\Utils as Suport;

trait Persistence {

    public function save() {
        $this->performSave();
    }

    public function performSave($loop = true) {
        $model = $this;

        if ($model->exists()) {
            $attributes = $model->getAttributesChanges();
            $columns = $this->filterColumns($attributes);

            $model->where([$model->getModelKeyName(), $model->getAttribute($model->getModelKeyName())])->update($columns);
        } else if ($model->incrementing) {
            $attributes = $model->getAttributes();
            $columns = $this->filterColumns($attributes);

            $id = $model->insertGetId($columns, $model->getModelKeyName());
            if ($id > 0) {
                $find = $this->classname::find($id);
                $this->config($find->attributes);
            }
        }

        $this->performSaveRelations();

        return $this;
    }    

    public function performSaveRelations() {
        $model = $this;
        $attributes = $model->getAttributesChanges();
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
