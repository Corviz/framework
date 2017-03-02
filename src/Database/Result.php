<?php

namespace Corviz\Database;

abstract class Result implements \Countable
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * The number of rows.
     *
     * @return int
     */
    abstract public function count() : int;

    /**
     * @return Row|null
     */
    abstract public function fetch();

    /**
     * @return array
     */
    abstract public function fetchAll() : array;

    /**
     * @return Connection
     */
    public function getConnection() : Connection
    {
        return $this->connection;
    }

    /**
     * Result constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
}
