<?php

namespace Corviz\Database;

class Row implements \ArrayAccess
{
    /**
     * @var array
     */
    private $data;

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * Not allowed to set values in a row
     */
    public function offsetSet($offset, $value)
    {
        //Do nothing
    }

    /**
     * Not allowed to unset values from a row
     */
    public function offsetUnset($offset)
    {
        //Do nothing
    }

    /**
     * Row constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}