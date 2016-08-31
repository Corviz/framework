<?php

namespace Corviz\Behaviour;

interface Runnable
{
    /**
     * @return mixed
     */
    public function run(... $args);
}
