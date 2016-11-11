<?php

namespace Corviz\Routing;

use Corviz\Http\Request;
use Corviz\String\ParametrizedString;

class Map
{
    /**
     * @var array
     */
    private static $routes = [];

    /**
     * @param Route $route
     */
    public static function addRoute(Route $route)
    {
        self::$routes [] = [
            'action'     => $route->getAction(),
            'alias'      => $route->getAlias(),
            'controller' => $route->getControllerName(),
            'methods'    => $route->getMethods(),
            'route'      => $route->getRouteStr(),
            'middlewareList' => $route->getMiddlewareList()
        ];
    }

    /**
     * Search for the route that matches the current
     * request. If not found, returns NULL.
     *
     * @return array|null
     */
    public static function getCurrentRoute()
    {
        $current = null;
        $request = Request::current();
        $routeStr = $request->getRouteStr();
        $method = $request->getMethod();

        //Search for the route
        foreach (self::$routes as $route) {
            //Check the method
            if (!in_array($method, $route['methods'])) {
                continue;
            }

            //Check number of slashes
            if (
                substr_count($route['route'], '/')
                != substr_count($routeStr, '/')
            ) {
                continue;
            }

            //Checks the route string
            if (
                ParametrizedString
                    ::make($route['route'])
                    ->matches($routeStr)
            ) {
                $current = $route;
                break;
            }
        }

        return $current;
    }

    /**
     * Map constructor.
     */
    private function __construct()
    {
    }
}
