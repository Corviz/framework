<?php

namespace Corviz\Database;

class Result implements \Countable
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
        return null;
    }
}
