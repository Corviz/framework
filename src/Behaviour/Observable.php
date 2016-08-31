<?php

namespace Corviz\Behaviour;

interface Observable
{
    /**
     * @param \Corviz\Behaviour\Observer $observer
     *
     * @return void
     */
    public static function register(Observer $observer);

    /**
     * @return void
     */
    public function notifyObservers();
}
