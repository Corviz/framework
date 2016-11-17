<?php

namespace Corviz\Behaviour;

interface Singleton
{
    /**
     * Get current object instance.
     *
     * @return static
     */
    public static function getInstance();
}