<?php

/**
 * Script Language
 * PHP version 7.4
 *
 * @category Language
 * @package  Punk\Query\Script
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Script\Languages;

use \Punk\Query\Script\Builder;
use \Punk\Query\Utils\Arr;
use \Punk\Query\Utils\Str;
use \Punk\Query\Utils\Container;
use \Punk\Query\Script\Clauses\Where;
use \Punk\Query\Script\Clauses\Join;
use \Punk\Query\Script\Expression;

class Language {

    protected string $escape_char = "";
    protected string $end_char = "";

    /**
     * Cria parâmentros (?|:name) para PDO de acordo com $values recebido
     * @param  Array  $values array de valores
     * @return array
     */
    public function createParameters($values): array {
        $parameters = [];
        $parameters_values = [];

        foreach (( is_array($values) ? $values : [$values]) as $key => $value) {
            $parameter_key = $this->extractParameter($key, $key);
            $parameters [] = $parameter_key;
            $parameters_values [$parameter_key] = $value;
        }

        return [$parameters, $parameters_values];
    }

    /**
     * Faz a compilacão string de todas as clausulas suportadas pelo driver
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    protected function compileClauses(Builder $query) {
        $sql = array();
        foreach ($this->getClauses() as $clause) {
            $compile = 'compile' . $clause;
            $sql [] = call_user_func_array([$this, $compile], [$query]);
        }
        return trim($this->concatenate($sql));
    }

    /**
     * Faz a compilacão da columns ou clausula Select 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    protected function compileColumns(Builder $query) {
        $distinct = $query->getDistinct();
        $columns = $query->getColumns();

        if (is_null($columns)) {
            $columns = ['*'];
        }

        return 'select '
                . ($distinct ? ' distinct ' : '')
                . implode(', ', $this->dymamicResolveGrammars($columns));
    }

    /**
     * Faz a compilacão da clausula From 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    protected function compileFrom(Builder $query) {
        $table = $query->getTable();

        if ($this->isExpression($table)) {
            return ' from ' . $table->getValue();
        }

        return ' from ' . $this->resolveGrammarTable($table . " as " . $query->getAliasTable());
    }

    /**
     * Faz a compilacão das clausulas Join 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    public function compileJoin(Builder $query) {
        $joins = $query->getJoins();

        if (count($joins) === 0) {
            return '';
        }

        return implode(' ', Arr::map($joins, function(Join $join) {
                    $type = $join->getType();
                    $compile = 'transpile' . $type . 'join';

                    if (method_exists($this, $compile)) {
                        return call_user_func_array([$this, $compile], [$join]);
                    } else {
                        $where = $this->compileExpressionWhere($join);
                        $from = trim(str_replace('from', '', $this->compileFrom($join)));
                        return $type . ' join ' . $from . ' on ' . $where;
                    }
                }));
    }

    /**
     * Faz a compilacão da clausula limit 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    protected function compileLimit(Builder $query) {
        $limit = $query->getLimit();

        if ($this->isExpression($limit)) {
            return $limit->getValue();
        }

        if ((int) $limit <= 0) {
            return '';
        }

        return ' limit ' . (int) $limit;
    }

    /**
     * Faz a compilacão das clausula Where 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    protected function compileWhere(Builder $query) {
        $wheres = $query->getWhere();

        if (count($wheres) === 0) {
            return '';
        }

        return ' where ' . $this->compileExpressionWhere($query);
    }

    /**
     * Faz a compilacão da expressões da Clausula Where 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    public function compileExpressionWhere(Builder $query) {
        $wheres = $query->getWhere();

        if (count($wheres) === 0) {
            return '';
        }

        return $this->concatenate(Arr::map($wheres, function(Container $where, int $index) {
                            $type = $where->getType();
                            $compile = 'compileExpressionWhere' . $type;
                            return call_user_func_array([$this, $compile], [$where, $index == 0]);
                        }));
    }

    /**
     * Faz a compilacão da Expressão Raw da Clausula Where 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    protected function compileExpressionWhereRaw(Where $where, bool $first) {
        $column = $where->getColumn();
        $operator = $where->getOperator();
        $boolean = $where->getBoolean();
        $assoc = $where->isAssociationColumn();
        $parameters = $where->getParameters();
        $parameters_values = $where->getBindings();
        $already_parameterized = $where->alreadyParameterized();

        return ($first ? '' : ' ' . $boolean . ' ')
                . ($assoc ? $column : $this->dymamicResolveGrammar($column))
                . ( ' ' . $operator . ' ' )
                . ($already_parameterized ? $this->concatenate(Arr::map($parameters, function ($parameter)use($parameters_values) {
                            return !is_int($parameters_values[$parameter]) && $parameters_values[$parameter] == null ? '' : (Arr::is_association($parameter) ? $parameter : ' ?');
                        })) : '');
    }

    /**
     * Faz a compilacão da Expressão IsNull|IsNotNull da Clausula Where 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    protected function compileExpressionWhereIsNull(Where $where, bool $first) {
        $column = $where->getColumn();
        $operator = $where->getOperator();
        $boolean = $where->getBoolean();
        $assoc = $where->isAssociationColumn();

        return ($first ? '' : ' ' . $boolean . ' ') .
                ($assoc ? $column : $this->dymamicResolveGrammar($column))
                . ' ' . $operator;
    }

    /**
     * Faz a compilacão da Expressão In|NotIn da Clausula Where 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    protected function compileExpressionWhereIn(Where $where, bool $first) {
        $column = $where->getColumn();
        $operator = $where->getOperator();
        $boolean = $where->getBoolean();
        $assoc = $where->isAssociationColumn();
        $parameters = $where->getParameters();
        $parameters_values = $where->getBindings();

        return ($first ? '' : ' ' . $boolean . ' ') .
                ($assoc ? $column : $this->dymamicResolveGrammar($column))
                . (' ' . $operator . ' ')
                . (' (' . $this->concatenate(Arr::map($parameters, function ($parameter)use($parameters_values) {
                            return !is_int($parameters_values[$parameter]) && $parameters_values[$parameter] == null ? '' : (Arr::is_association($parameter) ? $parameter : ' ?');
                        }), ', ') . ')');
    }

    /**
     * Faz a compilacão da Expressão {table}.{column} {operator} {table}.{column} da Clausula Where 
     * @param  Builder  $query QueryBuilder
     * @return string
     */
    protected function compileExpressionWhereColumns(Where $where, bool $first) {
        $column = $where->getColumn();
        $second_column = $where->getSecondColumn();
        $operator = $where->getOperator();
        $boolean = $where->getBoolean();
        $assoc = $where->isAssociationColumn();
        $assoc_value = $where->isAssociationValue();

        return ($first ? '' : ' ' . $boolean . ' ')
                . ($assoc ? $column : $this->dymamicResolveGrammar($column))
                . ( ' ' . $operator . ' ' )
                . ($assoc_value ? $second_column : $this->dymamicResolveGrammar($second_column));
    }

    protected function compileUnion(Builder $query) {
        $unions = $query->getUnions();

        if (count($unions) === 0) {
            return '';
        }

        return implode(' ', Arr::map($unions, function(Container $union, int $index)use($query) {
                    $all = $union->make('all');
                    $union_query = $union->make('query');
                    return 'Union' . ($all ? ' All ' : ' ') . $union_query;
                }));
    }

    /**
     * Concatena Array de strings
     * @param  array  $segments array de string
     * @param  string  $char_separator array de string
     * @return string
     */
    protected function concatenate($segments, $char_separator = ' ') {
        return implode($char_separator, array_filter($segments ?? [], function ($value) {
                    return (string) $value !== '';
                }));
    }

    /**
     * Cria um parâmentro(?|:name) para PDO de baesado no nome da coluna ($columnName) e no $key do valor recebido
     * @param  string  $columnName nome da coluna base do parâmentro
     * @param  string  $key do array de valores
     * @return string
     */
    public function extractParameter($columnName, $key): string {
        $parameter = $key;
        $lang = $this;

        if (!Arr::is_association($key) && !$lang->hasParameter($columnName) && !$lang->hasParameter($columnName, 2)) {
            $parameter = ':' . uniqid('parameter_');
        } else if (!$lang->hasParameter($columnName) && !$lang->hasParameter($columnName, 2) && Arr::is_association($key)) {
            $parameter = ':' . $key;
        }

        return $parameter;
    }

    /**
     * Cria um parâmentros (?|:name) para PDO de baesado
     * @param  string  $columnName nome da coluna base do parâmentro
     * @param  string  $values array de valores
     * @return array
     */
    public function extractParameters($columnName, $values): array {
        $parameters = [];
        $parameters_values = [];

        foreach (( is_array($values) ? $values : [$values]) as $key => $value) {
            $parameter_key = $this->extractParameter($columnName, $key);
            $parameters [] = $parameter_key;
            $parameters_values [$parameter_key] = $value;
        }

        return [$parameters, $parameters_values];
    }

    /**
     * Extrai Clausula Select 
     * @param  Builder $query QueryBuilder de origem
     * @return string
     */
    public function extractSelect(Builder $query): string {
        return $this->compileClauses($query);
    }

    /**
     * Extrai Clausula Insert com Parâmetros, as colunas para inserção serão definidas com base nos valores de entrada
     * @param  Builder $query QueryBuilder de origem
     * @param  array $values valores para inserção
     * @return array array com \Punk\Query\Script\Expression e parâmetros
     */
    public function extractInsert(Builder $query, $values = array()): array {

        $table = $this->resolveGrammarTable($query->getTable());

        $columns = $this->concatenate($this->dymamicResolveGrammars(array_keys($values)), ', ');

        [$parameters, $parameters_values] = $this->createParameters($values);

        $parameters_exp = $this->concatenate($parameters, ', ');

        return [new Expression("insert into $table ($columns) values ($parameters_exp)"), $parameters_values];
    }

    /**
     * Extrai Clausula Update com Parâmetros, as colunas para atualização serão definidas com base nos valores de entrada
     * @param  Builder $query QueryBuilder de origem
     * @param  array $values valores para inserção
     * @return array array com \Punk\Query\Script\Expression e parâmetros
     */
    public function extractUpdate(Builder $query, $values = array()): array {
        $table = $this->resolveGrammarTable($query->getTable());
        $where = $this->compileWhere($query);

        [$parameters, $parameters_values] = $this->createParameters($values);

        $parameters_exp = $this->concatenate(Arr::map($parameters, function ($parameter) {
                    return str_replace(":", '', $parameter) . " = $parameter";
                }), ', ');

        return [new Expression("update $table set $parameters_exp $where"), Arr::combineArray($parameters_values, $query->getBindings())];
    }

    /**
     * Extrai o Operador sendo utilizado em uma expressão SQL
     * @param  string $abstract Expressão
     * @return string
     */
    public function extractOperator($abstract): string {
        foreach ($this->getOperators() as $key => $value) {
            $operator = Arr::is_association($key) ? $key : $value;
            if (Str::isMatch((string) $abstract, sprintf("/(\%s)/", $operator))) {
                return $operator;
            }
        }
        return ' = ';
    }

    /**
     * Extrai a Paginação
     * @param  string $abstract Expressão
     * @return string
     */
    public function extractPagination(Builder $query, int $limitForPage, int $numberPage) {
        throw new \Exception('Função não implementada!!!!!!!!!!!!!');
    }

    /**
     * Retorna os Operadores suportados pelo Driver
     * @return array
     */
    public function getOperators(): array {
        return ['>=', '<=', '<>', '!=', '=', '>', '<', 'like', 'not like', 'is null', 'is not null', 'in', 'not in'];
    }

    /**
     * Array que informa quais Clausulas são suportadas pelo Driver e em qual ordem elas devem ser compiladas
     * @return array
     */
    public function getClauses(): array {
        return ['columns', 'from', 'join', 'where', 'limit', 'union'];
    }

    /**
     * Identifica se há parametros (?|:) definidos na expressão 
     * @param  string|\Punk\Query\Script\Expression $expression Expressão SQL
     * @param  int $paramType Tipo de parâmetro (?|:)
     * @return bool
     */
    public function hasParameter($expression, int $paramType = 1): bool {
        if ($this->isExpression($expression)) {
            $expression = $expression->getValue();
        }

        return Str::isMatch((string) $expression, $paramType == 1 ? '/\?/' : '/(:)\w*/');
    }

    /**
     * Identifica se há operadores definidos na expressão 
     * @param  string|\Punk\Query\Script\Expression $expression Expressão SQL 
     * @return bool
     */
    public function hasOperator($expression): bool {
        if (is_array($expression)) {
            return false;
        }

        if ($this->isExpression($expression)) {
            $expression = $expression->getValue();
        }

        foreach ($this->getOperators() as $key => $value) {
            $operator = Arr::is_association($key) ? $key : $value;
            if (Str::isMatch((string) $expression, sprintf("/(\%s)/", $operator))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Identifica se é uma Expression
     * @param  mixed $abstract Expressão SQL 
     * @return bool
     */
    public function isExpression($abstract): bool {
        return $abstract instanceof Expression;
    }

    public function resolveGrammar($value) {
        if (!($value == "*") && !empty($value)) {
            return $this->escape_char
                    . str_replace("$this->escape_char", "$this->escape_char$this->escape_char", $value)
                    . $this->escape_char;
        }

        return $value;
    }

    public function resolveGrammarTable($value) {
        if (Str::isMatch((string) $value, '/(^\w+)\.$/')) {
            return $value;
        }

        if (!empty($this->escape_char) && Str::isMatch((string) $value, sprintf('/%s(\w+)\%s/', $this->escape_char, $this->escape_char))) {
            return $value;
        }

        return $this->dymamicResolveGrammar($value);
    }

    protected function dymamicResolveGrammars($values) {
        return Arr::map($values, function($value) {
                    return $this->dymamicResolveGrammar($value);
                });
    }

    protected function dymamicResolveGrammar($value) {
        if ($this->isExpression($value)) {
            return $value->getValue();
        }

        if (strpos((string) $value, ' as ')) {
            return $this->dymamicResolveGrammarAlias($value);
        }

        return $this->dymamicResolveGrammarSegmentsName(explode('.', $value));
    }

    protected function dymamicResolveGrammarAlias($value) {
        $segments = preg_split('/\s+as\s+/i', $value);

        return $this->dymamicResolveGrammarSegmentsName($segments[0]) . ' as ' . $segments[1];
    }

    protected function dymamicResolveGrammarSegmentsName($values) {
        return implode('.', Arr::map($values, function($segment) {
                    return $this->resolveGrammar($segment);
                }));
    }

}
