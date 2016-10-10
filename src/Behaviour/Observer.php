<?php

namespace Corviz\Behaviour;

interface Observer
{
    /**
     * @param \Corviz\Behaviour\Observable $observable
     * @param array                        $data
     *
     * @return mixed
     */
    public function notify(Observable $observable, array $data);
}
