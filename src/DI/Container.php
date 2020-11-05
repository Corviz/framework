<?php

namespace Corviz\DI;

class Container
{
    /**
     * @var array
     */
    private $map = [];

    /**
     * @var array
     */
    private $singletonObjects = [];

    /**
     * @param string $name
     *
     * @throws \Exception
     *
     * @return object
     */
    public function get(string $name)
    {
        if ($this->isSingleton($name)) {
            /*
             * Object is instantiated as
             * singleton already. Just fetch it.
             */
            return $this->singletonObjects[$name];
        } elseif ($this->isDefined($name)) {
            /*
             * Creates a new instance
             * using map information.
             */
            return $this->build($name);
        } elseif (class_exists($name)) {
            /*
             * Class exists but it is
             * not mapped yet.
             */
            $this->set(
                $name,
                method_exists($name, '__construct') ?
                    $this->generateArgumentsMap($name) : []
            );

            return $this->get($name);
        }

        throw new \Exception("Couldn't create '$name'");
    }

    /**
     * Calls an object method,
     * injecting its dependencies.
     *
     * @param mixed  $obj
     * @param string $method
     * @param array  $params
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function invoke(
        $obj,
        string $method,
        array $params = []
    ) {
        if (is_string($obj)) {
            //Attempt to retrieve object from
            //the container.
            $obj = $this->get($obj);
        } elseif (!is_object($obj)) {
            //Not a valid argument.
            throw new \Exception('Invalid object');
        }

        $map = $this->generateArgumentsMap(
            $obj,
            $method,
            $params
        );
        $mapParams = $this->getParamsFromMap($map);

        return $obj->$method(...$mapParams);
    }

    /**
     * @param string $name
     * @param mixed  $definition
     *
     * @throws \Exception
     */
    public function set(string $name, $definition)
    {
        if ($this->isSingleton($name)) {
            throw new \Exception('Can\'t set a singleton twice.');
        }

        $this->map[$name] = $definition;
    }

    /**
     * @param string $name
     * @param mixed  $definition
     */
    public function setSingleton(string $name, $definition)
    {
        $this->set($name, $definition);
        $this->singletonObjects[$name] = $this->get($name);
    }

    /**
     * Build an object according to the map information.
     *
     * @param string $name
     *
     * @throws \Exception
     *
     * @return object
     */
    private function build(string $name)
    {
        $instance = null;
        $map = $this->map[$name];

        if (is_array($map)) {
            $params = $this->getParamsFromMap($map);
            $instance = new $name(...$params);
        } elseif ($map instanceof \Closure) {
            $instance = $map($this);
        } elseif (is_object($map)) {
            $instance = $map;
        } elseif (is_string($map)) {
            $instance = $this->get($map);
        } else {
            throw new \Exception('Invalid map');
        }

        return $instance;
    }

    /**
     * Generates a map that will be used by 'build()' method
     * to generate the args.
     *
     * @param mixed  $class
     * @param string $method
     * @param array  $predefined
     *
     * @throws \Exception
     *
     * @return array
     */
    private function generateArgumentsMap(
        $class,
        string $method = '__construct',
        array $predefined = []
    ): array {
        $arguments = [];
        $refMethod = new \ReflectionMethod($class, $method);

        /* @var $parameter \ReflectionParameter */
        foreach ($refMethod->getParameters() as $parameter) {
            $arg = [
                'value'   => null,
                'isClass' => false,
            ];

            if (isset($predefined[$parameter->getName()])) {
                //Get a predefined parameter
                $arg['value'] = $predefined[$parameter->getName()];
            } elseif ($parameter->isDefaultValueAvailable()) {
                //Parameter has a default value, just pass it
                $arg['value'] = $parameter->getDefaultValue();
            } elseif ($parameter->hasType()) {
                /* @var $pClass \ReflectionClass */
                $pClass = $parameter->getClass();

                //Only possible to pass get classes
                if (is_null($pClass)) {
                    $pName = $parameter->getName();

                    throw new \Exception("Parameter '$pName' is not a class");
                }

                $arg['value'] = $pClass->getName();
                $arg['isClass'] = true;
            } else {
                throw new \Exception('Could not define a value');
            }

            $arguments[] = $arg;
        }

        return $arguments;
    }

    /**
     * @param array $mapArray
     *
     * @return array
     */
    private function getParamsFromMap(array &$mapArray)
    {
        $params = [];

        foreach ($mapArray as $item) {
            if (!isset($item['isClass']) || !isset($item['value'])) {
                continue;
            }

            $params[] = $item['isClass'] ?
                $this->get((string) $item['value']) : $item['value'];
        }

        return $params;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function isDefined(string $name): bool
    {
        return isset($this->map[$name]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function isSingleton(string $name): bool
    {
        return isset($this->singletonObjects[$name]);
    }
}
