<?php

namespace Corviz\Database;

class Query
{
    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var array
     */
    private $sources;
}
