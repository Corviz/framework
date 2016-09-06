<?php

namespace Corviz\Behaviour;

interface Observer
{
    /**
     * @param \Corviz\Behaviour\Observable $observable
     * @param array ...$args
     *
     * @return mixed
     */
    public function notify(Observable $observable, ...$args);
}
