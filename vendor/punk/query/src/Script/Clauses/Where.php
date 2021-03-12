<?php

/**
 * Where Clause
 * PHP version 7.4
 *
 * @category Clauses
 * @package  Punk\Query\Script
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Script\Clauses;

use \Punk\Query\Script\Builder;
use \Punk\Query\Utils\Arr;
use \Punk\Query\Utils\Str;
use \Punk\Query\Utils\Container;

class Where extends Container {

    private Builder $parentBuilder;

    public function getColumn() {
        return $this->make('$columns');
    }

    public function getSecondColumn() {
        return $this->make('$second_column');
    }

    public function getType() {
        return $this->make('$type');
    }

    public function getBoolean() {
        return $this->make('$boolean');
    }

    public function getOperator() {
        return $this->make('$operator');
    }

    public function getParameters() {
        return $this->make('$parameters');
    }

    public function getBindings() {
        return $this->make('$parameters_values');
    }

    public function isAssociationColumn() {
        return $this->make('$assoc');
    }

    public function isAssociationValue() {
        return $this->make('$assoc_value');
    }

    public function alreadyParameterized() {
        return $this->make('$already_parameterized');
    }

    public function __construct(Builder $parentBuilder, string $type, int $major, array $options) {
        $this->parentBuilder = $parentBuilder;

        [$columns, $operator, $values, $boolean] = $this->readParams($options, $major);

        $this->bind('$columns', $columns);
        $this->bind('$type', $type);
        $this->bind('$boolean', $boolean);
        $this->bind('$operator', $operator);
        $this->bind('$values', $values);
        $this->preparedLang($this);
    }

    private function preparedLang(Container &$where): Container {
        $lang = $this->parentBuilder->getLang();
        $columns = $where->make('$columns');
        $operator = $where->make('$operator');
        $values = $where->make('$values');
        $boolean = $where->make('$boolean');
        $type = $where->make('$type');
        $assoc = true;
        $assoc_value = true;

        $this->preparedValueAndBoolean($values, $boolean);

        $this->preparedValueAndOperator($values, $operator);

        $this->preparedOperatorAndColumn($columns, $operator, $type, $values, $assoc, $assoc_value);

        [$parameters, $parameters_values] = $this->preparedParameters($columns, $values);

        $where->bind('$columns', $columns);
        $where->bind('$operator', $operator);
        $where->bind('$parameters_values', $parameters_values);
        $where->bind('$parameters', $parameters);
        $where->bind('$boolean', $boolean);
        $where->bind('$assoc', $assoc);
        $where->bind('$assoc_value', $assoc_value);
        $where->bind('$second_column', (Str::lower($type) === 'columns') ? $values : '');
        $where->bind('$already_parameterized', !$lang->hasParameter($columns) && !$lang->hasParameter($columns, 2));
        return $where;
    }

    private function preparedValueAndBoolean(&$values, &$boolean): void {
        if (empty($boolean)) {
            $boolean = ' and ';
            if (is_string($values) && Str::isMatch(Str::lower((string) $values), '(or|and)')) {
                $boolean = Str::lower((string) $values);
                $values = null;
            }
        }
    }

    private function preparedValueAndOperator(&$values, &$operator): void {
        $lang = $this->parentBuilder->getLang();
        if (empty($values) && !is_int($values)) {
            if ($lang->hasOperator($operator)) {
                $values = null;
            } else if (!empty($operator) || is_int($operator)) {
                $values = $operator;
                $operator = ' = ';
            }
        }
    }

    private function preparedOperatorAndColumn(&$columns, &$operator, &$type, &$values, &$assoc, &$assoc_value): void {
        $lang = $this->parentBuilder->getLang();
        if ($lang->hasOperator($columns)) {
            $operator = '';
        }

        if (Str::lower($type) === 'columns') {
            if (!$lang->hasOperator($values) && !$lang->hasParameter($values) && !$lang->hasParameter($values, 2)) {
                $values = trim((string) $values);
                $assoc_value = false;
            }
        }

        if (!$lang->hasOperator($columns) && !$lang->hasParameter($columns) && !$lang->hasParameter($columns, 2)) {
            $columns = trim($columns);
            $assoc = false;
        }
    }

    private function preparedParameters($columns, $values): array {
        return $this->parentBuilder->getLang()->extractParameters($columns, $values);
    }

    private function readParams(Array $options = [], $fields = 3) {
        if (Arr::is_association(Arr::key_first($options))) {
            $columns = Arr::key_first($options);
            $values = $options[Arr::key_first($options)];
        }
        switch ($fields) {
            case 2:
                $columns = isset($columns) ? $columns : Arr::findByIndex($options, 0);
                $boolean = Arr::findByIndex($options, 1);
                break;
            case 3:
                $values = isset($columns) ? $values : Arr::findByIndex($options, 1);
                $columns = isset($columns) ? $columns : Arr::findByIndex($options, 0);
                $boolean = Arr::findByIndex($options, 2);
                break;
            default:
                $values = isset($columns) ? $values : Arr::findByIndex($options, 2);
                $columns = isset($columns) ? $columns : Arr::findByIndex($options, 0);
                $operator = Arr::findByIndex($options, 1);
                $boolean = Arr::findByIndex($options, 3);
                break;
        }
        return [isset($columns) ? $columns : null, isset($operator) ? $operator : null, isset($values) ? $values : null, isset($boolean) ? $boolean : null];
    }

}
