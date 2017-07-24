<?php

namespace Corviz\Database;

use Corviz\Database\Query\Join;
use Corviz\Database\Query\WhereClause;

class Query
{
    /**
     * @var string
     */
    private $avgAggregate = '';

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $countAggregate = '';

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @var string
     */
    private $fromClause = '';

    /**
     * @var Join[]
     */
    private $joins = [];

    /**
     * @var string
     */
    private $maxAggregate = '';

    /**
     * @var string
     */
    private $minAggregate = '';

    /**
     * @var array
     */
    private $ordination = [];

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var int
     */
    private $queryLimit = null;

    /**
     * @var int
     */
    private $queryOffset = null;

    /**
     * @var null
     */
    private $queryUnion = null;

    /**
     * @var bool
     */
    private $queryUnionAll = false;

    /**
     * @var string
     */
    private $sumAggregate = '';

    /**
     * @var WhereClause
     */
    private $whereClause;

    /**
     * @param string $avgField
     *
     * @return $this
     */
    public function avg(string $avgField)
    {
        $this->avgAggregate = $avgField;

        return $this;
    }

    /**
     * @param $paramValue
     * @param null $paramKey
     */
    public function bind($paramValue, $paramKey = null)
    {
        if (is_null($paramKey)) {
            $this->parameters[] = $paramValue;
        } else {
            $this->parameters[$paramKey] = $paramValue;
        }
    }

    /**
     * @param string $countField
     *
     * @return $this
     */
    public function count(string $countField)
    {
        $this->countAggregate = $countField;

        return $this;
    }

    /**
     * @param array $params
     *
     * @return Result
     */
    public function execute(array $params = []) : Result
    {
        $params = array_replace($this->parameters, $params);

        if (
            empty($this->fields)
            && empty($this->avgAggregate)
            && empty($this->countAggregate)
            && empty($this->minAggregate)
            && empty($this->maxAggregate)
            && empty($this->sumAggregate)
        ) {
            $this->fields = ['*'];
        }

        return $this->connection->select($this, $params);
    }

    /**
     * Starts building a query.
     *
     * @param string $from
     *
     * @return Query
     */
    public function from(string $from)
    {
        $this->fromClause = $from;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvgAggregate(): string
    {
        return $this->avgAggregate;
    }

    /**
     * @return string
     */
    public function getCountAggregate(): string
    {
        return $this->countAggregate;
    }

    /**
     * @return array
     */
    public function getFields() : array
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getFrom() : string
    {
        return $this->fromClause;
    }

    /**
     * @return Join[]
     */
    public function getJoins() : array
    {
        return $this->joins;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->queryLimit;
    }

    /**
     * @return string
     */
    public function getMaxAggregate(): string
    {
        return $this->maxAggregate;
    }

    /**
     * @return string
     */
    public function getMinAggregate(): string
    {
        return $this->minAggregate;
    }

    /**
     * @return int|null
     */
    public function getOffset()
    {
        return $this->queryOffset;
    }

    /**
     * @return array
     */
    public function getOrdination() : array
    {
        return $this->ordination;
    }

    /**
     * @return array
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getSumAggregate(): string
    {
        return $this->sumAggregate;
    }

    /**
     * @return Query
     */
    public function getUnion() : Query
    {
        return $this->queryUnion;
    }

    /**
     * @return WhereClause
     */
    public function getWhereClause() : WhereClause
    {
        return $this->whereClause;
    }

    /**
     * @return bool
     */
    public function isUnionAll() : bool
    {
        return $this->queryUnionAll;
    }

    /**
     * @return bool
     */
    public function hasUnion() : bool
    {
        return !is_null($this->queryUnion);
    }

    /**
     * @param string        $table
     * @param \Closure|null $joinConstructor
     *
     * @return Query
     */
    public function join(string $table, \Closure $joinConstructor)
    {
        $join = new Join($table);
        $joinConstructor($join);

        $this->joins[] = $join;

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return Query
     */
    public function limit(int $limit)
    {
        $this->queryLimit = $limit;

        return $this;
    }

    /**
     * @param string $maxAggregate
     *
     * @return $this
     */
    public function max(string $maxAggregate)
    {
        $this->maxAggregate = $maxAggregate;

        return $this;
    }

    /**
     * @param string $minAggregate
     *
     * @return $this
     */
    public function min(string $minAggregate)
    {
        $this->minAggregate = $minAggregate;

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return Query
     */
    public function offset(int $offset)
    {
        $this->queryOffset = $offset;

        return $this;
    }

    /**
     * @param string $field
     * @param string $order
     *
     * @return Query
     */
    public function orderBy(string $field, $order = 'asc')
    {
        $order = strtolower($order) == 'asc' ? 'asc' : 'desc';
        $this->ordination[$field] = $order;

        return $this;
    }

    /**
     * The fields that will be selected.
     *
     * @param array ...$fields
     *
     * @return Query
     */
    public function select(...$fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param string $sumAggregate
     *
     * @return $this
     */
    public function sum(string $sumAggregate)
    {
        $this->sumAggregate = $sumAggregate;

        return $this;
    }

    /**
     * @param Query $query
     * @param bool  $isUnionAll
     *
     * @return Query
     */
    public function union(Query $query, $isUnionAll = false)
    {
        $this->queryUnion = $query;
        $this->queryUnionAll = $isUnionAll;

        return $this;
    }

    /**
     * @param Query $query
     *
     * @return Query
     */
    public function unionAll(Query $query)
    {
        return $this->union($query, true);
    }

    /**
     * Starts where clause.
     *
     * @param \Closure $constructor
     *
     * @return Query
     */
    public function where(\Closure $constructor)
    {
        $constructor($this->whereClause);

        return $this;
    }

    /**
     * Query constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->whereClause = new WhereClause();
    }
}
