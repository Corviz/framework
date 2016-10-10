<?php

namespace Corviz\Events;

use Corviz\Behaviour\Observable;
use Corviz\Behaviour\Observer;
use Corviz\Behaviour\Runnable;

abstract class EventHandler implements Observer, Runnable
{
    /**
     * {@inheritdoc}
     */
    final public function notify(Observable $observable, array $data)
    {
        if ($observable instanceof Event) {
            $this->run($observable, $data);
        }
    }

    abstract public function run(...$args);
}
