<?php

namespace Punk\Query\Fake\Capsule\Associations\Traits;

use \Punk\Query\Fake\Capsule\Capsule;
use \Punk\Query\Fake\Capsule\Associations\BelongsTo;
use \Punk\Query\Fake\Capsule\Associations\BelongsToMany;
use \Punk\Query\Fake\Capsule\Associations\HasMany;
use \Punk\Query\Fake\Capsule\Associations\HasOne;
use \Punk\Query\Fake\Capsule\Associations\Relation;
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

    /**
     * Get and Set variable Relations
     * @return and @param type
     * */
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

    /**
     * BelongsTo Relations
     * @param string $relationName Nome da propriedade que será relacionada com o Model
     * @param class $model Model
     * @param string $foreignKey
     * @param string $onwerkey
     * @return void
     * */
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

    /**
     * BelongsToMany Relations
     * @param string $relationName Nome da propriedade que será relacionada com o Model
     * @param class $model Model
     * @param class $table Tabela intermediária
     * @param string $sourcekey
     * @param string $destinyKey
     * @return void
     * */
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

    /**
     * HasMany Relations
     * @param string $relationName Nome da propriedade que será relacionada com o Model
     * @param class $model Model 
     * @param string $foreignKey
     * @param string $onwerkey
     * @return void
     * */
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

    /**
     * HasOne Relations
     * @param string $relationName Nome da propriedade que será relacionada com o Model
     * @param class $model Model 
     * @param string $foreignKey
     * @param string $onwerkey
     * @return void
     * */
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

    /**
     * Resolve Relations
     * Processa todos os relacionamentos de um Model, identifica quais relacionamentos que o model possui 
     * @return void
     * */
    public function resolveRelations() {
        $relations = $this->getRelations();
        foreach ($relations as $relationKey => $relation) {
            $type = Suporte\Arr::key_first($relation);
            $configs = array_values($relation[$type]);

            array_unshift($configs, $this->newBuilder()->clone(), $this);

            $this->resolvedRelations[$relationKey] = new $type(...$configs);
        }
    }

    /**
     * Verifica se no Model existe um relacioamento
     * @param string $relationKey relacionamento que está sendo procurado 
     * @return bool
     * */
    protected function hasRelation($relationKey): bool {
        return Suporte\Arr::key_exists($relationKey, $this->resolvedRelations);
    }

    /**
     * Verifica se um relacionamento já foi carregado no banco de dados
     * @param string $relationKey relacionamento que está sendo procurado 
     * @return bool
     * */
    protected function relationLoaded($relationKey): bool {
        return Suporte\Arr::key_exists($relationKey, $this->loadedRelations);
    }

    /**
     * Salva os modelos depententes de um relacionamento no banco de dados
     * @param string $relationKey relacionamento que está sendo procurado 
     * @param mixed $models modelos relacioanados ao objeto
     * @return void
     * */
    protected function saveRelation($relationKey, $models) {
        $relation = $this->getResolvedRelation($relationKey);
        $relation->save($models);
    }

    /**
     * Adciona um relacionamento a um Model/Class
     * @param string $class class/model 
     * @param string $name nome do relacionamento
     * @param mixed  $relationConfig configurações do relacionamento
     * @return void
     * */
    private static function addRelation(string $class, string $name, array $relationConfig) {
        if (!Suporte\Arr::key_exists($class, static::$relationships)) {
            static::$relationships[$class] = array();
        }
        static::$relationships[$class][$name] = $relationConfig;
    }

    /**
     * Retorna dos dados computados de um determinado relacionamento
     * @param string $relationKey nome do relacionamento 
     * @return mixed
     * */
    protected function getRelationValues($relationKey) {
        $relation = $this->getResolvedRelation($relationKey);
        $this->loadedRelations[$relationKey] = $relation;

        if ($this->isLoad($relation)) {
            return $relation->getRelationValue();
        }

        return $relation->getDefault();
    }

    /**
     * Seta dados em um determinado relaciomanamento
     * @param string $relationKey nome do relacionamento 
     * @param mixed $values valores que serão absorvidos pelo relacionamento
     * @return bool
     * */
    private function setRelationValues($relationKey, &$values) {
        $relation = $this->getResolvedRelation($relationKey);
        return $relation->setRelationValue($values);
    }

    /**
     * Verifica se um determinado relacioamento pode ser carregado
     * @param Relation $relation Relacionamento que vai passar pela verificação
     * @return bool
     * */
    private function isLoad(Relation $relation): bool {
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
