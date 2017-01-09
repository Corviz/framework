<?php

namespace Corviz\Database;

use Corviz\Database\Query\Join;
use Corviz\Database\Query\Where;

class Query
{
    /**
     * @var array
     */
    private $fields = ['*'];

    /**
     * @var string
     */
    private $from = '';

    /**
     * @var Join[]
     */
    private $joins = [];

    /**
     * @var int
     */
    private $queryLimit = null;

    /**
     * @var int
     */
    private $queryOffset = 0;

    /**
     * @var array
     */
    private $ordination = [];

    /**
     * @var Where
     */
    private $where;

    /**
     * Starts building a query.
     *
     * @param string $from
     *
     * @return Query
     */
    public static function from(string $from)
    {
        return new self($from);
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return Join[]
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @return int
     */
    public function getQueryLimit(): int
    {
        return $this->queryLimit;
    }

    /**
     * @return int
     */
    public function getQueryOffset(): int
    {
        return $this->queryOffset;
    }

    /**
     * @return array
     */
    public function getOrdination(): array
    {
        return $this->ordination;
    }

    /**
     * @return Where
     */
    public function getWhere(): Where
    {
        return $this->where;
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

    public function orWhere()
    {
        //TODO implement method.
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

    public function where()
    {
        //TODO implement method.
    }

    /**
     * Query constructor.
     *
     * @param string $from
     */
    private function __construct(string $from)
    {
        $this->from = $from;
        $this->where = new Where();
    }
}
