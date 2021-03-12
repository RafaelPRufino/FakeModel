<?php

/**
 * Script Builder
 * PHP version 7.4
 *
 * @category Script
 * @package  Punk\Query\Script
 * @author   Rafael Pereira <rafaelrufino>
 * @license  http://www.gnu.org/copyleft/gpl.html GPL
 * @link     https://github.com/RafaelPRufino/QueryPHP
 */

namespace Punk\Query\Script;

use \Punk\Query\Connections\ConnectionInterface;
use \Punk\Query\Script\Languages\Language;
use \Punk\Query\Utils\Container;
use \Punk\Query\Utils\Arr;
use \Punk\Query\Utils\Str;
use \Punk\Query\Script\Clauses\Join;
use \Punk\Query\Script\Clauses\Where;

class Builder {

    /**
     * variable connectioninterface$connection
     * @return \Punk\Query\Connections\ConnectionInterface
     * */
    protected ConnectionInterface $connection;

    /**
     * Get and Set variable  Connection
     * @return \Punk\Query\Connections\ConnectionInterface
     * */
    public function getConnection(): ConnectionInterface {
        return $this->connection;
    }

    /**
     * Get and Set variable  Connection
     * @param \Punk\Query\Connections\ConnectionInterface
     * */
    protected function setConnection(ConnectionInterface $newConnectioninterface) {
        $this->connection = $newConnectioninterface;
    }

    /**
     * variable languageLang
     * type LanguageInterface
     * */
    private Language $languageLang;

    /**
     * Get and Set variable languageLang
     * @return Language
     * */
    public function getLang(): Language {
        return $this->languageLang;
    }

    /**
     * Get and Set variable languageLang
     * @param Language $newLanguageLang
     * */
    protected function setLang(Language $newLanguageLang) {
        $this->languageLang = $newLanguageLang;
    }

    public function __construct(ConnectionInterface $connection, Language $lang) {
        $this->setConnection($connection);
        $this->setLang($lang);
    }

    /**
     * variable arrayBindings
     * type Array
     * */
    private $arrayBindings = array('select' => [], 'from' => [], 'join' => [], 'where' => [], 'union' => []);

    /**
     * Get and Set variable arrayBindings
     * @return and @param type Array
     * */
    public function getBindings(): Array {
        return Arr::flatten($this->arrayBindings);
    }

    /**
     * Set values in arrayBindings
     * @return  \Punk\Query\Script\Builder
     * */
    public function setBindings(string $keyBind, $newArrayBindings): self {
        $bindings = $this->arrayBindings;

        if (!array_key_exists($keyBind, $bindings)) {
            $bindings [$keyBind] = array();
        }

        $bindings [$keyBind] = Arr::combineArray($bindings [$keyBind], Arr::toArray($newArrayBindings));
        $this->arrayBindings = $bindings;
        return $this;
    }

    /**
     * variable $limit
     * @return int
     * */
    protected $limit = -1;

    /**
     * variable $$limit
     * @return int
     * */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * Informa que será efetuado um limit no Select
     * @return \Punk\Query\Script\Builder
     */
    public function limit($limit = -1) {
        $this->limit = $limit;
        return $this;
    }

    #region user description {

    /**
     * variable $from
     * @return
     * */
    private $from = null;

    /**
     * variable $columns
     * @return array
     * */
    public function getFrom() {
        return $this->from;
    }

    /**
     * Retorna o verdadeiro no da tabela
     * @return string
     * */
    public function getTable() {
        return $this->getfrom()[0];
    }

    /**
     * Retorna o nome Final\Alias Tabela
     * @return string
     * */
    public function getAliasTable(): string {
        return $this->getfrom()[1] ?? $this->getTable();
    }

    /**
     * Retorna um ScriptBuilder
     * @param string|\Punk\Query\Script\Builder $table 
     * @param string $as
     * @return \Punk\Query\Script\Builder
     */
    public function from($table, $as = null): Builder {

        if (is_array($table)) {
            $this->from = $table;
            return $this;
        } else
        if (self::isQueryable($table)) {
            return $this->fromSub($table);
        }

        $this->from = [$table, $as];
        if (is_null($as) || empty($as) || $as = '') {
            $this->from = [$table, $table];
        }
        return $this;
    }

    /**
     * Informa um select como fonte dos dados 
     * @param string|\Punk\Query\Script\Builder $table 
     * @param string $as 
     * @return \Punk\Query\Script\Builder
     * */
    public function fromSub($table, $as): self {
        [$query, $bindings] = $this->createSub($table);
        $alias = $this->getLang()->resolveGrammar($as);
        $this->from = [new Expression('(' . $query . ') as ' . $alias), $alias];
        $this->setBindings('from', $bindings);
        return $this;
    }

    #endregion }

    /**
     * variable $distinct
     * @return bool
     * */
    protected bool $distinct = false;

    /**
     * variable $distinct
     * @return bool
     * */
    public function getDistinct(): bool {
        return $this->distinct;
    }

    /**
     * Informa que será efetuado um distinct
     * @return \Punk\Query\Script\Builder
     */
    public function distinct(): self {
        $this->distinct = true;
        return $this;
    }

    /**
     * variable $columns
     * @return array
     * */
    private array $columns = ['*'];

    /**
     * variable $columns
     * @return array
     * */
    public function getColumns(): array {
        return $this->columns;
    }

    /**
     * Informa os campos do select
     * @param array|\Punk\Query\Script\Builder $columns Colunas do Select
     * @return \Punk\Query\Script\Builder
     */
    public function select($columns = null): self {
        $this->columns = [];

        if (!is_array($columns) || !isset($columns)) {
            $columns = ['*'];
        }

        $this->columns = $columns;
        return $this;
    }

    /**
     * variable $where
     * @return array|mixed
     * */
    protected array $where = [];

    /**
     * variable $where
     * @return array
     * */
    public function getWhere(): array {
        return $this->where;
    }

    /**
     * Realiza um where Dinâmico
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function where(Array $options = []): self {
        $where = $this->newWhereClause('Raw', 4, $options);
        $this->where [] = $where;
        $this->setBindings('where', $where->getBindings());
        return $this;
    }

    /**
     * Realiza um where Dinâmico
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhere(Array $options = []): self {
        $this->where($options);
        $where = end($this->where);
        $where->bind('$boolean', 'or');
        reset($this->where);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function whereIsNull(Array $options = []): self {
        $where = $this->newWhereClause('IsNull', 2, $options);
        $where->bind('$operator', 'is null');
        $where->bind('$parameters', []);
        $where->bind('$parameters_values', []);
        $this->where [] = $where;
        $this->setBindings('where', $where->getBindings());
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhereIsNull(Array $options = []): self {
        $this->whereIsNull($options);
        $where = end($this->where);
        $where->bind('$boolean', 'or');
        reset($this->where);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Not Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function whereIsNotNull(Array $options = []): self {
        $where = $this->newWhereClause('IsNull', 2, $options);
        $where->bind('$operator', 'is not null');
        $where->bind('$parameters', []);
        $where->bind('$parameters_values', []);
        $this->where [] = $where;
        $this->setBindings('where', $where->getBindings());
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Not Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhereIsNotNull(Array $options = []): self {
        $this->whereIsNotNull($options);
        $where = end($this->where);
        $where->bind('$boolean', 'or');
        reset($this->where);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function whereColumns(Array $options = []): self {
        $where = $this->newWhereClause('Columns', 4, $options);
        $this->where [] = $where;
        $this->setBindings('where', []);
        return $this;
    }

    /**
     * Realiza um where com a clausula Is Null
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhereColumns(Array $options = []): self {
        $this->whereColumns($options);
        $where = end($this->where);
        $where->bind('$boolean', 'or');
        reset($this->where);
        return $this;
    }

    /**
     * Realiza um where com a clausula In
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function whereIn(Array $options = []): self {
        $where = $this->newWhereClause('In', 3, $options);
        $where->bind('$operator', 'in');
        $this->where [] = $where;
        $this->setBindings('where', $where->getBindings());
        return $this;
    }

    /**
     * Realiza um where com a clausula In
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhereIn(Array $options = []): self {
        $this->whereIn($options);
        $where = end($this->where);
        $where->bind('$boolean', 'or');
        reset($this->where);
        return $this;
    }

    /**
     * Realiza um where com a clausula Not In
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function whereNotIn(Array $options = []): self {
        $where = $this->newWhereClause('NotIn', 3, $options);
        $where->bind('$operator', 'not in');
        $this->where [] = $where;
        $this->setBindings('where', $where->getBindings());
        return $this;
    }

    /**
     * Realiza um where com a clausula Not In
     * @param Array $options configuração do where
     * @return \Punk\Query\Script\Builder
     */
    public function orWhereNotIn(Array $options = []): self {
        $this->whereNotIn($options);
        $where = end($this->where);
        $where->bind('$boolean', 'or');
        reset($this->where);
        return $this;
    }

    /**
     * Cria uma Clausula Where
     * @param string $type tipo de where que será realizado
     * @param string $major número colunas de where que será realizado
     * @param array|mixed $options opções do where
     * @return \Punk\Query\Script\Clausules\Where retorna um Clausula Where
     * */
    protected function newWhereClause(string $type, int $major, array $options): Where {
        return new Where($this, $type, $major, $options);
    }

    /**
     * variable joins
     * @return array|\Punk\Query\Script\Clauses\Join
     * */
    private array $joins = [];

    /**
     * variable $joins
     * @return array|\Punk\Query\Script\Clauses\Join
     * */
    public function getJoins(): array {
        return $this->joins;
    }

    public function crossJoin($table, $first, $operator = null, $second = null, $boolean = 'and'): self {
        return $this->join($table, $first, $operator, $second, $boolean, 'cross');
    }

    public function fullOuterJoin($table, $first, $operator = null, $second = null, $boolean = 'and'): self {
        return $this->join($table, $first, $operator, $second, $boolean, 'fullouter');
    }

    public function innerJoin($table, $first, $operator = null, $second = null, $boolean = 'and'): self {
        return $this->join($table, $first, $operator, $second, $boolean, 'inner');
    }

    public function leftJoin($table, $first, $operator = null, $second = null, $boolean = 'and'): self {
        return $this->join($table, $first, $operator, $second, $boolean, 'left');
    }

    public function rightJoin($table, $first, $operator = null, $second = null, $boolean = 'and'): self {
        return $this->join($table, $first, $operator, $second, $boolean, 'right');
    }

    protected function join($table, $first, $operator = null, $second = null, $boolean = 'and', $type = 'inner'): self {
        $join = $this->newJoinClause($type, $table);

        if ($first instanceof \Closure) {
            $first($join);
        } else {
            $join->on([$first, $operator, $second, $boolean]);
        }

        $this->joins [] = $join;
        $this->setBindings('join', $join->getBindings());

        return $this;
    }

    /**
     * Cria uma Clausula Join
     * @param string $type tipo de join que será realizado
     * @param mixed $table tabela|query com a qual será realizado o join
     * @return \Punk\Query\Script\Clausules\Join retorna um Clausula Join
     * */
    protected function newJoinClause(string $type, $table): Join {
        return new Join($this, $type, $table);
    }

    /**
     * variable $unions
     * @return array
     * */
    private array $unions = [];

    /**
     * Retorna Array com Clausulas Unions compiladas $unions
     * @return array
     * */
    public function getUnions(): array {
        return $this->unions;
    }

    /**
     * Realiza um union entre duas Query
     * @param \Punk\Query\Script\Builder $query query com a qual será realizada um union
     * @param boolean $all identifica se será realizado um union comun ou all
     * @return \Punk\Query\Script\Builder
     * */
    public function union(self $query, $all = false): self {
        $union = New Container();
        [$sql, $bindings] = $this->createSub($query);

        $union->bind('query', $sql);
        $union->bind('all', $all);
        $this->setBindings('union', $bindings);
        $this->unions [] = $union;
        return $this;
    }

    /**
     * Realiza um union all entre duas Query
     * @param \Punk\Query\Script\Builder $query query com a qual será realizada um union 
     * @return \Punk\Query\Script\Builder
     * */
    public function unionAll(self $query): self {
        return $this->union($query, true);
    }

    /**
     * Realiza insert dos atributos no banco de dados
     * @param \Punk\Query\Script\Builder $attributes bindings
     * @return bool
     * */
    public function insert(array $attributes): bool {
        [$query, $bindings] = $this->getLang()->extractInsert($this->clone(), $attributes);
        return $this->connection->execute($query->getValue(), $bindings, function($statement) {
                    return $statement->rowCount() == 1;
                });
    }

    /**
     * Realiza insert dos atributos no banco de dados
     * @param \Punk\Query\Script\Builder $attributes bindings
     * @param string $keyId primary key
     * @return int
     * */
    public function insertGetId(array $attributes, $keyId = null): int {
        [$query, $bindings] = $this->getLang()->extractInsert($this->clone(), $attributes);
        return $this->connection->execute($query->getValue(), $bindings, function($statement)use($keyId) {
                    return $this->getConnection()->lastInsertId($keyId);
                });
    }

    /**
     * Realiza atualização dos atributos no banco de dados
     * @param \Punk\Query\Script\Builder $attributes bindings
     * @return bool
     * */
    public function update(array $attributes): bool {
        [$query, $bindings] = $this->getLang()->extractUpdate($this->clone(), $attributes);
        return $this->connection->execute($query->getValue(), $bindings, function($statement) {
                    return $statement->rowCount() == 1;
                });
    }

    /**
     * Realiza um count
     * @param string $columns coluna a ser computada 
     * @return int
     * */
    public function count($columns = '*') {
        $builder = $this->clone()->select([new Expression('count(' . $columns . ') as count_value')]);
        $values = $this->connection->select($builder->toSQL(), $builder->getBindings());
        return $values ? array_shift($values)['count_value'] : 0;
    }

    /**
     * Realiza um avg
     * @param string $columns coluna a ser computada 
     * @return int
     * */
    public function avg($columns = '*') {
        $builder = $this->clone()->select([new Expression('avg(' . $columns . ') as avg_valeu')]);
        $values = $this->connection->select($builder->toSQL(), $builder->getBindings());
        return $values ? array_shift($values)['avg_valeu'] : 0;
    }

    /**
     * Realiza um sum
     * @param string $columns coluna a ser computada 
     * @return int
     * */
    public function sum($columns = '*') {
        $builder = $this->clone()->select([new Expression('sum(' . $columns . ') as sum_valeu')]);
        $values = $this->connection->select($builder->toSQL(), $builder->getBindings());
        return $values ? array_shift($values)['sum_valeu'] : 0;
    }

    /**
     * Realiza um sum
     * @param string $columns coluna a ser computada 
     * @return int
     * */
    public function page(int $limitForPage = 20, int $numberPage = 1) {       
        $this->limit($this->getLang()->extractPagination($this, $limitForPage, $numberPage)); 
        return $this;
    }

    /**
     * Cria um SubQuery
     * @param \Punk\Query\Script\Builder $query Query onde será feita a sub
     * @return array
     * */
    public function createSub($query) {
        if (self::isQueryable($query)) {
            return [$query->toSQL(), $query->getBindings()];
        } else if (is_string($query)) {
            return [(string) $query, []];
        }
    }

    /**
     * Get a new instance of the query builder.
     *
     * @return \Punk\Query\Script\Builder
     */
    public function newQuery(): self {
        return new static($this->getConnection(), $this->getLang());
    }

    /**
     * Get a new instance of the query builder bt from.
     *
     * @return \Punk\Query\Script\Builder
     */
    public function forQuery(): self {
        return $this->newQuery()->from($this->getFrom());
    }

    /**
     * Faz um clone do \Punk\Query\Script\Builder 
     * @return \Punk\Query\Script\Builder clone de \Punk\Query\Script\Builder
     * */
    public function clone(): self {
        return clone $this;
    }

    /**
     * Retorna um Select Query compilado
     * @return string
     * */
    public function toSQL(): string {
        return $this->getLang()->extractSelect($this->clone());
    }

    /**
     * Executa a QueryBuilder no DataBase
     * @return array|mixed
     * */
    public function runSelect() {
        return $this->connection->select($this->toSQL(), $this->getBindings());
    }

    /**
     * Executa a QueryBuilder no DataBase
     * @return PDOStatement
     * */
    public function statement() {
        return $this->connection->statement($this->toSQL(), $this->getBindings());
    }

    /**
     * Determina se um objeto é um QueyBuilder
     * @return bool
     * */
    public static function isQueryable($abstract): bool {
        return $abstract instanceof self || $abstract instanceof Builder;
    }

}
