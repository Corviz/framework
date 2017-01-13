<?php

namespace Corviz\Database\Query;

class WhereClause
{
    /**
     * @var array
     */
    private $clauses = [];

    /**
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->clauses);
    }

    /**
     * @param string $field
     * @param string $operator
     * @param mixed  $value
     *
     * @return WhereClause
     */
    public function where(string $field, string $operator, $value)
    {
        $this->addClause('where', [$field, $operator, $value]);
        return $this;
    }

    /**
     * Create a new clause
     *
     * @param string $type
     * @param $value
     */
    private function addClause(string $type, $value)
    {
        $this->clauses[] = [
            'type' => $type,
            'value' => $value,
        ];
    }
}
