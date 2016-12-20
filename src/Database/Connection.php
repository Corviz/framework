<?php

namespace Corviz\Database;

interface Connection
{
    /**
     * Number of affected rows by the last
     * INSERT, UPDATE or DELETE query.
     *
     * @return int
     */
    public function affectedRows() : int;

    /**
     * Begin a database transaction.
     *
     * @return bool
     */
    public function begin() : bool;

    /**
     * Commit transaction.
     *
     * @return bool
     */
    public function commit() : bool;

    /**
     * Start a connection.
     *
     * @param array ...$options
     *
     * @return mixed
     */
    public function connect(...$options) : bool;

    /**
     * Inform if the current connection is active.
     *
     * @return bool
     */
    public function connected() : bool;

    /**
     * Execute query.
     *
     * @param Query $query
     * @param array $params
     *
     * @return Result|null
     */
    public function execute(Query $query, array $params = []);

    /**
     * The id of the last stored document.
     *
     * @return string
     */
    public function lastId() : string;

    /**
     * Rollback transaction.
     *
     * @return bool
     */
    public function rollback() : bool;
}
