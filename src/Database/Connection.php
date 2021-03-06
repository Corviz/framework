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
    abstract public function affectedRows(): int;

    /**
     * Begin a database transaction.
     *
     * @return bool
     */
    abstract public function begin(): bool;

    /**
     * Commit transaction.
     *
     * @return bool
     */
    abstract public function commit(): bool;

    /**
     * Start a connection.
     *
     * @return mixed
     */
    abstract public function connect(): bool;

    /**
     * Inform if the current connection is active.
     *
     * @return bool
     */
    abstract public function connected(): bool;

    /**
     * @return Query
     */
    final public function createQuery(): Query
    {
        return new Query($this);
    }

    /**
     * @param Model $model
     *
     * @return Result
     */
    abstract public function delete(Model $model): Result;

    /**
     * @return string
     */
    abstract public function getDateFormat(): string;

    /**
     * The id of the last stored document.
     *
     * @return string
     */
    abstract public function lastId(): string;

    /**
     * Execute a native query.
     *
     * @param array ...$args
     *
     * @return Result
     */
    abstract public function nativeQuery(...$args): Result;

    /**
     * Rollback transaction.
     *
     * @return bool
     */
    abstract public function rollback(): bool;

    /**
     * Insert the model data in it's respective
     * table or collection.
     *
     * @param Model $model
     *
     * @return Result
     */
    abstract public function insert(Model $model): Result;

    /**
     * Execute a select (or find) operation according
     * to the parameters provided by the query.
     *
     * @param Query $query
     * @param array $params
     *
     * @return Result
     */
    abstract public function select(Query $query, array $params): Result;

    /**
     * Update the model data in it's respective
     * table or collection.
     *
     * @param Model $model
     *
     * @return Result
     */
    abstract public function update(Model $model): Result;

    /**
     * Connection constructor.
     *
     * @param array $options
     */
    abstract public function __construct(array $options);
}
