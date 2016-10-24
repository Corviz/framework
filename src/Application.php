<?php

namespace Corviz;

use Corviz\Behaviour\Runnable;
use Corviz\DI\Container;
use Corviz\Http\Request;
use Corviz\Mvc\ControllerDispatcher;
use Corviz\Routing\Map;
use Corviz\String\ParametrizedString;

/**
 * Main app controller.
 */
class Application implements Runnable
{
    /**
     * @var Application
     */
    private static $current;

    /**
     * @var array
     */
    private $configs = [];

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $directory;

    /**
     * @var Request
     */
    private $request;

    /**
     * Get the current running application.
     *
     * @return \Corviz\Application
     */
    public static function current() : Application
    {
        return self::$current;
    }

    /**
     * @return \Corviz\DI\Container
     */
    public function getContainer() : Container
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getDirectory() : string
    {
        return $this->directory;
    }

    /**
     * {@inheritdoc}
     */
    public function run(...$args)
    {
        //TODO: turn code into parts (methods)
        self::$current = $this;

        //Load application definitions.
        $this->loadRoutes();
        $this->registerRequestParsers();

        //Call controller action.
        $this->request = Request::current();
        $route = Map::getCurrentRoute();

        if ($route) {
            $controllerPrefix = $this->getConf('app')['controllersPrefix'];
            $routeStr = ParametrizedString::make(
                $route['route']
            );

            $params = array_replace(
                $routeStr->getValues($this->request->getRouteStr()),
                $this->request->getQueryParams()
            );

            ControllerDispatcher::dispatch(
                $controllerPrefix.$route['controller'],
                $route['action'],
                $params
            );
        }

        self::$current = null;
    }

    /**
     * Load configurations from $conf basename file.
     *
     * @param $conf
     *
     * @return mixed
     */
    private function getConf($conf)
    {
        if (!isset($this->configs[$conf])) {
            $file = $this->getDirectory()."configs/$conf.php";
            $this->configs[$conf] = require $file;
        }

        return $this->configs[$conf];
    }

    /**
     * Load user defined routes.
     */
    private function loadRoutes()
    {
        require $this->getDirectory().'application/routes.php';
    }

    /**
     * Load request parsers.
     */
    private function registerRequestParsers()
    {
        $parsers = $this->getConf('app')['requestParsers'];
        foreach ($parsers as $parser) {
            Request::registerParser($parser);
        }
    }

    /**
     * Application constructor.
     *
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = realpath($directory).'/';
        $this->container = new Container();
    }
}
