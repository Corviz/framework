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
     * @param array  $params
     *
     * @return WhereClause
     */
    public function and(string $field, string $operator, string $field2, array $params = null)
    {
        $this->addClause(
            'where',
            compact('field', 'operator', 'field2'),
            $params
        );
        return $this;
    }

    /**
     * @param string $value
     * @param string $field1
     * @param string $field2
     * @param array  $params
     *
     * @return WhereClause
     */
    public function between(string $value, string $field1, string $field2, array $params = null)
    {
        $this->addClause(
            'between',
            compact('value', 'field1', 'field2'),
            $params
        );
        return $this;
    }

    /**
     * @return array
     */
    public function getClauses() : array
    {
        return $this->clauses;
    }

    /**
     * @param string $field
     * @param array  $values
     * @param array  $params
     *
     * @return WhereClause
     */
    public function in(string $field, array $values, array $params = null)
    {
        $this->addClause(
            'in',
            compact('field', 'values'),
            $params
        );
        return $this;
    }

    /**
     * @param Query $query
     * @param array $params
     *
     * @return WhereClause
     */
    public function inQuery(string $field, Query $query, array $params = null)
    {
        $this->addClause(
            'inQuery',
            compact('field', 'query'),
            $params
        );
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->clauses);
    }

    /**
     * @param \Closure $constructor
     */
    public function nested(\Closure $constructor)
    {
        $whereClause = new WhereClause();
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
     * @param array  $params
     *
     * @return WhereClause
     */
    public function or(string $field, string $operator, string $field2, array $params = null)
    {
        $this->and($field, $operator, $field2, $params);
        $this->convertToOrJunction();
        return $this;
    }

    /**
     * @param string $value
     * @param string $field1
     * @param string $field2
     * @param array  $params
     *
     * @return WhereClause
     */
    public function orBetween(string $value, string $field1, string $field2, array $params = null)
    {
        $this->between($value, $field1, $field2, $params);
        $this->convertToOrJunction();
        return $this;
    }

    /**
     * @param string $field
     * @param array $values
     * @param array|null $params
     *
     * @return WhereClause
     */
    public function orIn(string $field, array $values, array $params = null)
    {
        $this->in($field, $values, $params);
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
     * @param array  $params
     */
    private function addClause(string $type, $value, array $params = null)
    {
        $junction = 'and';
        $params = $params ?: [];
        $this->clauses[] = compact('type','value', 'junction','params');
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
