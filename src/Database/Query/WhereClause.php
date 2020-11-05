<?php

namespace Corviz\Database\Query;

use Corviz\Database\Query;

class WhereClause
{
    /**
     * @var array
     */
    private $clauses = [];

    /**
     * @param string $field
     * @param string $operator
     * @param string $field2
     *
     * @return WhereClause
     */
    public function and(string $field, string $operator, string $field2)
    {
        $this->addClause(
            'where',
            compact('field', 'operator', 'field2')
        );

        return $this;
    }

    /**
     * @param string $value
     * @param string $field1
     * @param string $field2
     *
     * @return WhereClause
     */
    public function between(string $value, string $field1, string $field2)
    {
        $this->addClause(
            'between',
            compact('value', 'field1', 'field2')
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getClauses(): array
    {
        return $this->clauses;
    }

    /**
     * @param string $field
     * @param array  $values
     *
     * @return WhereClause
     */
    public function in(string $field, array $values)
    {
        $this->addClause(
            'in',
            compact('field', 'values')
        );

        return $this;
    }

    /**
     * @param Query $query
     *
     * @return WhereClause
     */
    public function inQuery(string $field, Query $query)
    {
        $this->addClause(
            'inQuery',
            compact('field', 'query')
        );

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->clauses);
    }

    /**
     * @param \Closure $constructor
     */
    public function nested(\Closure $constructor)
    {
        $whereClause = new self();
        $constructor($whereClause);

        $this->addClause('nested', compact('whereClause'));
    }

    /**
     * @param \Closure $constructor
     *
     * @return WhereClause
     */
    public function not(\Closure $constructor)
    {
        $where = new static();
        $constructor($where);

        $this->addClause(
            'not',
            compact('where')
        );

        return $this;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param string $field2
     *
     * @return WhereClause
     */
    public function or(string $field, string $operator, string $field2)
    {
        $this->and($field, $operator, $field2);
        $this->convertToOrJunction();

        return $this;
    }

    /**
     * @param string $value
     * @param string $field1
     * @param string $field2
     *
     * @return WhereClause
     */
    public function orBetween(string $value, string $field1, string $field2)
    {
        $this->between($value, $field1, $field2);
        $this->convertToOrJunction();

        return $this;
    }

    /**
     * @param string $field
     * @param array  $values
     *
     * @return WhereClause
     */
    public function orIn(string $field, array $values)
    {
        $this->in($field, $values);
        $this->convertToOrJunction();

        return $this;
    }

    /**
     * @param \Closure $constructor
     *
     * @return $this
     */
    public function orNested(\Closure $constructor)
    {
        $this->nested($constructor);
        $this->convertToOrJunction();

        return $this;
    }

    /**
     * Create a new clause.
     *
     * @param string $type
     * @param mixed  $value
     */
    private function addClause(string $type, $value)
    {
        $junction = 'and';
        $this->clauses[] = compact('type', 'value', 'junction');
    }

    /**
     * Set the last inserted clause junction as OR.
     */
    private function convertToOrJunction()
    {
        $index = count($this->clauses) - 1;
        $this->clauses[$index]['junction'] = 'or';
    }
}
