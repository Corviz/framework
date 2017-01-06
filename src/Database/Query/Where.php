<?php

namespace Corviz\Database\Query;

class Where
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
}
