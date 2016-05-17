<?php

namespace Corviz\Routing;


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
        self::$routes []= $route;
    }

    /**
     * @return Route[]
     */
    public static function getRoutes()
    {
        return self::$routes;
    }  

    public function __construct(){}

}