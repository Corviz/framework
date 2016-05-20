<?php

namespace Corviz\Routing;


use \Closure;
use Corviz\String\ParameterizedString;

final class Route
{

    const TYPE_GET = "get";
    const TYPE_POST = "post";
    const TYPE_PUT = "put";
    const TYPE_DELETE = "delete";

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
     * @var string
     */
    private $type;

    /**
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function delete(string $string, Closure $closure, string $alias = null)
    {
        self::addToMap(self::TYPE_DELETE, $string, $closure, $alias);
    }
    
    /**
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function get(string $string, Closure $closure, string $alias = null)
    {
        self::addToMap(self::TYPE_GET, $string, $closure, $alias);
    }

    /**
     * @param string $prefix
     * @param Closure $closure
     */
    public static function group(string $prefix, Closure $closure)
    {
        array_push(self::$groupStack, $prefix);
        call_user_func($closure);
        array_pop(self::$groupStack);
    }

    /**
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function post(string $string, Closure $closure, string $alias = null)
    {
        self::addToMap(self::TYPE_POST, $string, $closure, $alias);
    }

    /**
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    public static function put(string $string, Closure $closure, string $alias = null)
    {
        self::addToMap(self::TYPE_PUT, $string, $closure, $alias);
    }

    /**
     * @param string $type
     * @param string $string
     * @param Closure $closure
     * @param string|null $alias
     */
    private static function addToMap(string $type, string $string, Closure $closure, string $alias = null)
    {
        $route = new Route();
        $route->setType($type);
        $route->setParameterizedString(self::generateParameterizedString($string));
        $route->setClosure($closure);
        $route->setAlias($alias);
        Map::addRoute($route);
    }

    /**
     * @param string $rawString
     * @return ParameterizedString
     */
    private static function generateParameterizedString(string $rawString) : ParameterizedString
    {
        $sep = "/";
        $startsWithBackslash = substr($rawString, 0, 1) == $sep;
        
        //prepend backslash
        if(!$startsWithBackslash){
            $rawString = $sep.$rawString;
        }

        //append backslash
        if(substr($rawString, -1) != $sep){
            $rawString .= $sep;
        }

        //prepend group pieces
        if(!empty(self::$groupStack)){
            $rawString = $sep.implode($sep, self::$groupStack).$rawString;
        }
        
        //generate and return the object
        return ParameterizedString::make($rawString);
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
    public function getType()
    {
        return $this->type;
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
    public function setParameterizedString($parameterizedString)
    {
        $this->parameterizedString = $parameterizedString;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    private function __construct(){}

}