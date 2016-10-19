<?php

namespace Corviz\Routing;

use Closure;
use Corviz\Http\Request;

final class Route
{
    const REGEXP_VALIDATE_STRING = '/^(?!.*{([\\w-]+)}.*{\\1})(\\/?(([a-zA-Z0-9\\_\\-]+)|(\\{[a-zA-Z][a-zA-Z0-9]*\\}))\\/?)*$/';
    const SEPARATOR = '/';

    /**
     * @var string
     */
    private $action;

    /**
     * @var array
     */
    private static $groupStack = [];

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $controllerName;

    /**
     * @var array
     */
    private $methods;

    /**
     * @var string
     */
    private $routeStr;

    /**
     * Creates a route that listens to all supported.
     *
     * @param string $routeStr
     * @param array  $info
     */
    public static function all(
        string $routeStr,
        array $info
    ) {
        $methods = Request::getValidMethods();
        self::create($methods, $routeStr, $info);
    }

    /**
     * Build a new route and add it to the Map.
     *
     * @param array  $methods  Array containing http methods.
     *                         The supported methods are defined by Request::METHOD_* constants
     * @param string $routeStr A string that may contain parameters
     *                         that will be passed to the controller.
     *                         For example:
     *                         - /home
     *                         - /product/{productId}
     *                         - /tag/{slug}
     * @param array  $info     An array containing the following indexes:
     *                         - controller: Name of a controller class
     *                         - action: Method of the defined controller (Default: index)
     *                         - alias: Short name of the route, for easy referencing
     */
    public static function create(
        array $methods,
        string $routeStr,
        array $info
    ) {
        $route = new self();
        $route->setMethods($methods);
        $route->setAction(isset($info['action']) ? $info['action'] : 'index');
        $route->setAlias(isset($info['alias']) ? $info['alias'] : '');
        $route->setControllerName($info['controller']);
        $route->setMethods($methods);
        $route->setRouteStr($routeStr);

        Map::addRoute($route);
    }

    /**
     * Creates a route that listens to DELETE http method.
     *
     * @param string $routeStr
     * @param array  $info
     */
    public static function delete(
        string $routeStr,
        array $info
    ) {
        self::create([Request::METHOD_DELETE], $routeStr, $info);
    }

    /**
     * Creates a route that listens to GET http method.
     *
     * @param string $routeStr
     * @param array  $info
     */
    public static function get(
        string $routeStr,
        array $info
    ) {
        self::create([Request::METHOD_GET], $routeStr, $info);
    }

    /**
     * Creates a route group.
     *
     * @param string  $prefix
     * @param Closure $closure
     */
    public static function group(string $prefix, Closure $closure)
    {
        $prefix = trim($prefix, self::SEPARATOR);

        if ($prefix) {
            array_push(self::$groupStack, $prefix);
        }

        //Call group closure
        $closure();

        if ($prefix) {
            array_pop(self::$groupStack);
        }
    }

    /**
     * Creates a route that listens to POST http method.
     *
     * @param string $routeStr
     * @param array  $info
     */
    public static function post(
        string $routeStr,
        array $info
    ) {
        self::create([Request::METHOD_POST], $routeStr, $info);
    }

    /**
     * Creates a route that listens to PATCH http method.
     *
     * @param string $routeStr
     * @param array  $info
     */
    public static function patch(
        string $routeStr,
        array $info
    ) {
        self::create([Request::METHOD_PATCH], $routeStr, $info);
    }

    /**
     * Creates a route that listens to PUT http method.
     *
     * @param string $routeStr
     * @param array  $info
     */
    public static function put(
        string $routeStr,
        array $info
    ) {
        self::create([Request::METHOD_PUT], $routeStr, $info);
    }

    /**
     * Valid routes have alphanumeric literal strings or parameters
     * separated by slashes (/) and also does not repeat variable names
     * Example of valid routes:
     *  - "user/{id}"
     *  - "category/car".
     *
     * @param string $routeStr
     */
    private static function validateRoute(string $routeStr)
    {
        if ($routeStr == '/') {
            return;
        }

        if (!$routeStr) {
            throw new \InvalidArgumentException("Route can't be empty");
        }

        if (!preg_match(self::REGEXP_VALIDATE_STRING, $routeStr)) {
            throw new \InvalidArgumentException("Invalid route: $routeStr");
        }
    }

    /**
     * @return string
     */
    public function getAction() : string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getAlias() : string
    {
        return $this->alias;
    }

    /**
     * @return string
     */
    public function getControllerName() : string
    {
        return $this->controllerName;
    }

    /**
     * @return array
     */
    public function getMethods() : array
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getRouteStr() : string
    {
        return $this->routeStr;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action)
    {
        $this->action = $action;
    }

    /**
     * @param string $alias
     */
    public function setAlias(string $alias)
    {
        $this->alias = $alias;
    }

    /**
     * @param string $controllerName
     */
    public function setControllerName(string $controllerName)
    {
        $this->controllerName = $controllerName;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;
    }

    /**
     * @param string $routeStr
     */
    public function setRouteStr(string $routeStr)
    {
        $sep = self::SEPARATOR;
        $routeStr = trim($routeStr, $sep);

        //normalize string
        if ($routeStr) {
            $routeStr .= $sep;
        }
        $routeStr = $sep.$routeStr;

        //prepend group pieces
        if (!empty(self::$groupStack)) {
            $groupStr = implode($sep, self::$groupStack);
            $routeStr = $sep.$groupStr.$routeStr;
        }

        //validate route
        self::validateRoute($routeStr);

        //assign route string
        $this->routeStr = $routeStr;
    }

    private function __construct()
    {
    }
}
