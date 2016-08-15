<?php

namespace Corviz\Behaviour;

interface ConvertsToJson
{
    /**
     * Returns the current object
     * json representation.
     *
     * @return mixed
     */
    public function toJson();
}
