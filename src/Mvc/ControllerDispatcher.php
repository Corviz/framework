<?php

namespace Corviz\Mvc;

use ReflectionMethod;

class ControllerDispatcher
{
    /**
     * @param string $controllerName
     * @param string $action
     * @param array $parameters
     *
     * @return mixed
     */
    public static function dispatch(
        string $controllerName,
        string $action = 'index',
        array $parameters = []
    ) {
        $controller = new $controllerName();

        //Search for pre-requisites
        self::validate($controller, $action);

        //Mount parameters array
        $paramsArray = array_replace(
            self::getDefaultValues($controllerName, $action),
            $parameters
        );

        return $controller->$action(...$paramsArray);
    }

    /**
     * @param string $className
     * @param $method
     *
     * @return array
     */
    private static function getDefaultValues(
        string $className,
        string $method
    ) : array {
        $refM = new ReflectionMethod($className, $method);
        $defaultValues = [];

        foreach ($refM->getParameters() as $param) {
            $val = null;

            if ($param->isDefaultValueAvailable()) {
                $val = $param->getDefaultValue();
            }

            $defaultValues[$param->name] = $val;
        }

        return $defaultValues;
    }

    /**
     * @param \Corviz\Mvc\Controller $controller
     * @param string $action
     */
    private static function validate(
        $controller,
        string $action
    ) {
        //Check for invalid class
        if (! $controller instanceof Controller) {
            throw new \InvalidArgumentException(
                'Invalid controller'
            );
        }

        //Check for action
        if (! method_exists($controller, $action)) {
            throw new \InvalidArgumentException(
                "Action not found: $action"
            );
        }
    }
}