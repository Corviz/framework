<?php

namespace Corviz\Routing;


use \Closure;
use Corviz\Http\Request;
use Corviz\String\ParameterizedString;

final class Route
{

    const REGEXP_VALIDATE_STRING = "/^(?!.*{([\\w-]+)}.*{\\1})(\\/?(([a-zA-Z0-9\\_\\-]+)|(\\{[a-zA-Z][a-zA-Z0-9]*\\}))\\/?)*$/";

    /**
     * @var array
     */
    private static $groupStack = [];

    /**
     * @var string
     */
    private $alias;

    /**
     * @var Closure
     */
    private $closure;

    /**
     * @var ParameterizedString
     */
    private $parameterizedString;

    /**
     * @var array
     */
    private $methods;

    /**
     * @param array $methods
     *      Array containing http methods.
     *      The supported methods are defined by Request::METHOD_* constants
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function create(array $methods, string $string, Closure $closure, string $alias = null)
    {
        $route = new Route();
        $route->setMethods($methods);
        $route->setParameterizedString(self::generateParameterizedString($string));
        $route->setClosure($closure);
        $route->setAlias($alias);
        Map::addRoute($route);
    }

    /**
     * Creates a route that listens to DELETE http method
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function delete(string $string, Closure $closure, string $alias = null)
    {
        self::create([Request::METHOD_DELETE], $string, $closure, $alias);
    }
    
    /**
     * Creates a route that listens to GET http method
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function get(string $string, Closure $closure, string $alias = null)
    {
        self::create([Request::METHOD_GET], $string, $closure, $alias);
    }

    /**
     * Creates a route group
     * @param string $prefix
     * @param Closure $closure
     */
    public static function group(string $prefix, Closure $closure)
    {
        array_push(self::$groupStack, trim($prefix, "/"));
        call_user_func($closure);
        array_pop(self::$groupStack);
    }

    /**
     * Creates a route that listens to POST http method
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function post(string $string, Closure $closure, string $alias = null)
    {
        self::create([Request::METHOD_POST], $string, $closure, $alias);
    }

    /**
     * Creates a route that listens to PATCH http method
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function patch(string $string, Closure $closure, string $alias = null)
    {
        self::create([Request::METHOD_PATCH], $string, $closure, $alias);
    }

    /**
     * Creates a route that listens to PUT http method
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function put(string $string, Closure $closure, string $alias = null)
    {
        self::create([Request::METHOD_PUT], $string, $closure, $alias);
    }

    /**
     * @param string $rawString
     * @return ParameterizedString
     */
    private static function generateParameterizedString(string $rawString) : ParameterizedString
    {
        $sep = "/";
        
        //normalize the string
        $str = $sep.trim($rawString, $sep).$sep;

        //prepend group pieces
        if(!empty(self::$groupStack)){
            $groupStr = $sep . implode($sep, self::$groupStack);
            $str = $groupStr.$str;
        }

        self::validateRoute($str);

        //generate and return the object
        return ParameterizedString::make($str);
    }

    /**
     * Valid routes have alphanumeric literal strings or parameters
     * separated by slashes (/) and also does not repeat variable names
     * Example of valid routes:
     *  - "user/{id}"
     *  - "category/car"
     *
     * @param string $routeStr
     */
    private static function validateRoute(string $routeStr)
    {
        if(!preg_match(self::REGEXP_VALIDATE_STRING, $routeStr)){
            throw new \InvalidArgumentException("Invalid route: $routeStr");
        }
    }

    /**
     * @return Closure
     */
    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * @return ParameterizedString
     */
    public function getParameterizedString()
    {
        return $this->parameterizedString;
    }

    /**
     * @return string
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @param Closure $closure
     */
    public function setClosure($closure)
    {
        $this->closure = $closure;
    }

    /**
     * @param ParameterizedString $parameterizedString
     */
    public function setParameterizedString(ParameterizedString $parameterizedString)
    {
        $this->parameterizedString = $parameterizedString;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods)
    {
        $this->methods = $methods;
    }

    private function __construct(){}

}