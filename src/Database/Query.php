<?php

namespace Corviz\Database;

use Corviz\Database\Query\Join;
use Corviz\Database\Query\WhereClause;

class Query
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var array
     */
    private $fields = ['*'];

    /**
     * @var string
     */
    private $fromClause = '';

    /**
     * @var Join[]
     */
    private $joins = [];

    /**
     * @var array
     */
    private $ordination = [];

    /**
     * @var int
     */
    private $queryLimit = null;

    /**
     * @var int
     */
    private $queryOffset = 0;

    /**
     * @var null
     */
    private $queryUnion = null;

    /**
     * @var WhereClause
     */
    private $whereClause;

    /**
     * @param array $params
     *
     * @return Result
     */
    public function execute(array $params = []) : Result
    {
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
     * @return int
     */
    public function getQueryLimit() : int
    {
        return $this->queryLimit;
    }

    /**
     * @return int
     */
    public function getQueryOffset() : int
    {
        return $this->queryOffset;
    }

    /**
     * @return Query
     */
    public function getQueryUnion() : Query
    {
        return $this->queryUnion;
    }

    /**
     * @return array
     */
    public function getOrdination() : array
    {
        return $this->ordination;
    }

    /**
     * @return WhereClause
     */
    public function getWhereClause() : WhereClause
    {
        return $this->whereClause;
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
     * @param Query $query
     *
     * @return Query
     */
    public function union(Query $query)
    {
        $this->queryUnion = $query;

        return $this;
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
