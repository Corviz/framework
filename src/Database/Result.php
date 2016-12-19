<?php

namespace Corviz\Database;

class Result implements \Iterator
{
    /**
     * The number of rows.
     *
     * @return int
     */
    public function count() : int
    {
        return 0;
    }

    /**
     * @return Row|null
     */
    public function fetch()
    {
    }

    /**
     * @return mixed
     */
    public function current()
    {
        // TODO: Implement current() method.
    }

    /**
     * @return mixed
     */
    public function next()
    {
        // TODO: Implement next() method.
    }

    /**
     * @return mixed
     */
    public function key()
    {
        // TODO: Implement key() method.
    }

    /**
     * @return mixed
     */
    public function valid()
    {
        // TODO: Implement valid() method.
    }

    /**
     * @return mixed
     */
    public function rewind()
    {
        // TODO: Implement rewind() method.
    }
}
