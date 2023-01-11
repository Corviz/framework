<?php

namespace Corviz;

use Closure;
use Corviz\Behaviour\Runnable;
use Corviz\DI\Container;
use Corviz\DI\Provider;
use Corviz\Http\Middleware;
use Corviz\Http\Request;
use Corviz\Http\Response;
use Corviz\Http\ResponseFactory;
use Corviz\Mvc\Controller;
use Corviz\Routing\Map;
use Corviz\String\ParametrizedString;
use Exception;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

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
     * Load configurations from $conf file.
     *
     * @param string $conf
     *
     * @return mixed
     */
    public function config(string $conf)
    {
        if (!isset($this->configs[$conf])) {
            $file = $this->getDirectory()."configs/$conf.php";
            $this->configs[$conf] = require $file;
        }

        return $this->configs[$conf];
    }

    /**
     * Get the current running application.
     *
     * @return Application
     */
    public static function current(): self
    {
        return self::$current;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * {@inheritdoc}
     */
    public function run(...$args)
    {
        //@TODO: turn code into parts (submethods)
        self::$current = $this;
        $this->container->set(self::class, $this);

        //Load application definitions.
        $this->registerProviders();

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
                throw new Exception("Invalid controller: {$route['controller']}");
            }

            /*
             * Call controller and process midware list
             */
            $middlewareList = $this->buildMiddlewareQueue([
                $route['middlewareList'],
                $controller->getMiddlewareList(),
            ]);

            $application = $this;
            $fn = function () use ($controller, &$params, &$route, $application) {
                $response = $application
                    ->getContainer()
                    ->invoke($controller, $route['action'], $params);

                return ResponseFactory::build($response);
            };

            $response = $this->proccessMiddlewareQueue($middlewareList, $fn);
            $response->send();
        }

        self::$current = null;
    }

    /**
     * @param callable $proccess
     * @return void
     * @throws Exception
     */
    public function background(callable $proccess)
    {
        self::$current = $this;
        $this->container->set(self::class, $this);

        //Load application definitions.
        $this->registerProviders();

        $proccess = Closure::fromCallable($proccess);
        $this->container->invoke($proccess, '__invoke');

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
        $middlewareList = $this->config('app')['middleware'];

        $groupsIterator = new RecursiveIteratorIterator(
            new RecursiveArrayIterator($groups)
        );

        foreach ($groupsIterator as $middleware) {
            $current = $middlewareList[$middleware];

            foreach ((array) $current as $curr) {
                $queue[] = $curr;
            }
        }

        return $queue;
    }

    /**
     * @param array    $queue
     * @param Closure $controllerClosure
     *
     * @throws Exception
     *
     * @return Response
     */
    private function proccessMiddlewareQueue(array $queue, Closure $controllerClosure)
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
                    throw new Exception("Invalid middleware: '$middleware'");
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
     * Register application providers.
     */
    private function registerProviders()
    {
        $providers = $this->config('app')['providers'];
        foreach ($providers as $provider) {
            $obj = new $provider($this);

            if (!$obj instanceof Provider) {
                throw new Exception("Invalid provider: $provider");
            }

            $this->container->invoke($obj, 'register');
        }
    }

    /**
     * Application constructor.
     *
     * @param string $directory
     */
    final public function __construct(string $directory)
    {
        $this->directory = realpath($directory).'/';
        $this->container = new Container();
    }
}
