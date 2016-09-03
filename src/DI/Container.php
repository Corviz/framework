<?php

namespace Corviz\DI;

class Container
{
    private $map = [];
    private $singletonObjects = [];

    /**
     * @param string $name
     *
     * @throws \Exception
     *
     * @return object
     */
    public function get(string $name) : object
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
            $this->generateMap($name);
            return $this->get($name);

        }

        throw new \Exception("Couldn't create '$name'");
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
     * @param string $name
     */
    private function generateMap(string $name)
    {
        //TODO implementation
    }

    /**
     * @param string $name
     *
     * @return object
     */
    private function build(string $name) : object
    {
        $map = $this->map[$name];
        $instance = null;

        //TODO build logic

        return $instance;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function isDefined(string $name) : bool
    {
        return isset($this->map[$name]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function isSingleton(string $name) : bool
    {
        return isset($this->singletonObjects[$name]);
    }
}
