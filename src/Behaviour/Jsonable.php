<?php

namespace Corviz\Behaviour;

interface Jsonable
{
    /**
     * Returns the current object
     * json representation.
     *
     * @return mixed
     */
    public function toJson();
}
