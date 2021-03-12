<?php

/**
 * Traits
 * PHP version 7.4
 *
 * @category Utils
 * @package  Punk\Query
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Utils\Traits;

use \Punk\Query\Utils\Arr;
use \Punk\Query\Utils\Str;

trait Attributes {

    /**
     * Bingding Changes Data
     * @return array
     * */
    protected $changes = [];

    /**
     * Pega atributos que foram alterados 
     * @return array
     * */
    public function getAttributesChanges() {
        return Arr::queryBy($this->changes, function($value, $key) {
                    return $key !== $this->getModelKeyName();
                });
    }

    public function getAttributes() {
        $relations = $this->getResolvedRelations();

        foreach ($relations as $relation) {
            $this->getAttribute($relation->getRelationName());
        }

        return $this->attributes;
    }

    public function clearChanges(): void {
        $this->changes = [];
    }

    /**
     * Bingding Attributes Data
     * @return array
     * */
    public $attributes = [];

    /**
     * Preenche uma classe com atributos vindos de array
     * @param Array $data
     * @return void
     * */
    protected function fill($data): void {
        if (empty($data)) {
            return;
        }

        foreach ($data AS $key => $value) {
            if (!Arr::is_association($key)) {
                continue;
            }
            $this->setAttribute($key, $value);
        }
    }

    /**
     * Informa de um atributo está bloqueado para leitura
     * @param string $key
     * @return bool
     * */
    public function attributeBlocked(string $key) {
        return false;
    }

    /**
     * Pega o valor do atributo $key
     * @param string $key
     * @return mixed
     * */
    public function getAttribute(string $key) {
        if ($this->isGetAttributeMethod($key) || $this->isAttribute($key) || $this->isRelationship($key)) {
            return $this->getAttributeValue($key);
        }

        if (method_exists(self::class, $key)) {
            return;
        }

        return null;
    }

    /**
     * Pega o valor do atributo $key
     * @param string $key atributo para qual deseja pegar valor
     * @return mixed
     * */
    protected function getAttributeValue(string $key) {
        if ($this->isGetAttributeMethod($key)) {
            $method = $this->methodGetAttributeName($key);
            return $this->$method();
        } else if ($this->isRelationship($key)) {
            return $this->getAttributeRelationValue($key);
        } else if ($this->isAttribute($key)) {
            return $this->attributes[$key];
        }

        return null;
    }

    /**
     * Pega o valor do atributo $key
     * @param string $key atributo para qual deseja pegar valor
     * @return mixed
     * */
    protected function getAttributeRelationValue(string $key) {
        $original = $this->isAttribute($key) ? $this->attributes[$key] : null;
        if (!$this->relationLoaded($key)) {
            $newValue = $this->getRelationValues($key);
            if (is_array($newValue)) {
                $newValue = Arr::combineArray($newValue, $original);
            }
            $this->attributes[$key] = $newValue;
        }
        return $this->attributes[$key];
    }

    /**
     * Seta um valor para um atributo $key
     * @param string $key atributo para qual deseja passar valor
     * @return mixed
     * */
    public function setAttribute(string $key, $value) {
        return $this->setAttributeValue($key, $value);
    }

    /**
     * Seta um valor para um atributo $key
     * @param string atributo para qual deseja passar valor
     * @return mixed
     * */
    protected function setAttributeValue(string $key, $value) {
        $original = $this->getAttribute($key);

        if ($this->isSetAttributeMethod($key)) {
            $method = $this->methodSetAttributeName($key);
            $this->$method($value);
        } else if ($this->isRelationship($key)) {
            $this->attributes[$key] = $this->setRelationValues($key, $value);
        } else {
            $this->attributes [$key] = $value;
        }

        if ($value != $original) {
            $this->changes[$key] = $this->attributes[$key];
        }

        return $this;
    }

    /**
     * Verifica se é um atributo padrão
     * @param string $key atributo para qual deseja passar valor
     * @return bool
     * */
    protected function isAttribute(string $key): bool {
        return isset($this->attributes[$key]) && Arr::key_exists($key, $this->attributes);
    }

    /**
     * Verifica se é um atributo magico
     * @param string $key atributo para qual deseja passar valor
     * @return bool
     * */
    protected function isGetAttributeMethod(string $key): bool {
        return method_exists($this, $this->methodGetAttributeName($key));
    }

    /**
     * Verifica se é um atributo magico
     * @param string $key atributo para qual deseja passar valor
     * @return bool
     * */
    protected function isSetAttributeMethod(string $key): bool {
        return method_exists($this, $this->methodSetAttributeName($key));
    }

    protected function isMethodMagic(string $method): bool {
        return Str::isMatch($method, '/^(get|set)(\w+)(Attribute)$/m');
    }

    protected function methodGetAttributeName(string $key) {
        return 'get' . $key . 'Attribute';
    }

    protected function methodSetAttributeName(string $key) {
        return 'set' . $key . 'Attribute';
    }

    public function isRelationship(string $key): bool {
        return $this->hasRelation($key);
    }

    public function jsonSerialize() {
        $relations = $this->getResolvedRelations();
        $methods = Arr::queryBy(get_class_methods($this), function($method) {
                    return Str::isMatch($method, '/^(get)(\w+)(Attribute)$/m');
                });
        $methods_values = [];

        foreach ($relations as $relation) {
            $this->getAttribute($relation->getRelationName());
        }

        foreach ($methods as $method) {
            Str::isMatch($method, '/^(get)(\w+)(Attribute)$/m', $groups); 
            $methods_values [$groups[2]] = $this->$method($this);
        }

        return Arr::combineArray($this->attributes, $methods_values);
    }

}
