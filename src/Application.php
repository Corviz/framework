<?php

namespace Corviz;

use Corviz\Behaviour\Runnable;
use Corviz\DI\Container;
use Corviz\Http\Middleware;
use Corviz\Http\Request;
use Corviz\Mvc\Controller;
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
            $routeStr = ParametrizedString::make(
                $route['route']
            );

            $params = array_replace(
                $routeStr->getValues($this->request->getRouteStr()),
                $this->request->getQueryParams()
            );

            $controller = $this->container->get($route['controller']);

            //Check controller
            if (!$controller instanceof Controller) {
                throw new \Exception("Invalid controller: {$route['controller']}");
            }

            /*
             * Call controller and process midware list
             */
            $middlewareList = $this->buildMiddlewareQueue([
                $route['middlewareList'],
                $controller->getMiddlewareList(),
            ]);

            $fn = function () use ($controller, &$params, &$route) {
                return Application::current()
                    ->getContainer()
                    ->invoke($controller, $route['action'], $params);
            };

            $this->proccessMiddlewareQueue($middlewareList, $fn);
        }

        self::$current = null;
    }

    /**
     * @param array $groups
     *
     * @return array
     */
    private function buildMiddlewareQueue(array $groups)
    {
        $queue = [];
        $middlewareList = $this->getConf('app')['middleware'];

        $groupsIterator = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($groups)
        );

        foreach ($groupsIterator as $middleware) {
            $current = $middlewareList[$middleware];

            if (is_array($current)) {
                $queue += $current;
            } else {
                $queue [] = $current;
            }
        }

        return $queue;
    }

    /**
     * @param array    $queue
     * @param \Closure $controllerClosure
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function proccessMiddlewareQueue(array $queue, \Closure $controllerClosure)
    {
        $return = null;

        if (!empty($queue)) {
            //Proccess queue
            $previousFn = $controllerClosure;

            foreach ($queue as $middleware) {
                //Middleware instance
                $obj = $this->container->get($middleware);

                if ($obj instanceof Middleware) {
                    $fn = function () use ($obj, $previousFn) {
                        return $obj->handle($previousFn);
                    };
                    $previousFn = $fn;
                } else {
                    throw new \Exception("Invalid middleware: '$middleware'");
                }
            }

            $return = $previousFn();
        } else {
            //Run controller immediately
            $return = $controllerClosure();
        }

        return $return;
    }

    /**
     * Load configurations from $conf basename file.
     *
     * @param string $conf
     *
     * @return mixed
     */
    private function getConf(string $conf)
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
        $parsers = $this->getConf('app')['requestParser'];
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
