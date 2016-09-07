<?php

namespace Corviz\Mvc;

use Corviz\Application;

class ControllerDispatcher
{
    /**
     * @param string $controllerName
     * @param string $action
     * @param array  $parameters
     *
     * @return mixed
     */
    public static function dispatch(
        string $controllerName,
        string $action = 'index',
        array $parameters = []
    ) {
        return Application::current()->getContainer()
            ->invoke($controllerName, $action, $parameters);
    }
}
