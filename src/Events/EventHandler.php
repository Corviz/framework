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
    final public function notify(Observable $observable, ...$args)
    {
        if ($observable instanceof Event) {
            $this->run($observable, ...$args);
        }
    }

    abstract public function run(...$args);
}
