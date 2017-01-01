<?php

namespace Corviz\Database;

use Corviz\Mvc\Model;

abstract class Connection
{
    /**
     * Number of affected rows by the last
     * INSERT, UPDATE or DELETE query.
     *
     * @return int
     */
    abstract public function affectedRows() : int;

    /**
     * Begin a database transaction.
     *
     * @return bool
     */
    abstract public function begin() : bool;

    /**
     * Commit transaction.
     *
     * @return bool
     */
    abstract public function commit() : bool;

    /**
     * Start a connection.
     *
     * @param array ...$options
     *
     * @return mixed
     */
    abstract public function connect(...$options) : bool;

    /**
     * Inform if the current connection is active.
     *
     * @return bool
     */
    abstract public function connected() : bool;

    /**
     * @param Model $model
     *
     * @return Result
     */
    abstract public function delete(Model $model) : Result;

    /**
     * The id of the last stored document.
     *
     * @return string
     */
    abstract public function lastId() : string;

    /**
     * Rollback transaction.
     *
     * @return bool
     */
    abstract public function rollback() : bool;

    /**
     * Save the model data in its respective
     * table or collection.
     *
     * @param Model $model
     *
     * @return Result
     */
    abstract public function save(Model $model) : Result;

    /**
     * Execute a select (or find) operation according
     * to the parameters provided by the query.
     *
     * @param Query $query
     *
     * @return Result
     */
    abstract public function select(Query $query) : Result;
}
