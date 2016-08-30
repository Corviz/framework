<?php

namespace Corviz\DI;

class Container
{
    /**
     * @var \SplObjectStorage
     */
    private $dependencies;

    /**
     * @var array
     */
    private $map = [];

    /**
     * @param string $name
     * @param string $className
     * @param string|array $args
     */
    public function set(
        string $name,
        string $className,
        $args = [],
        bool $asSingleton = true
    ) {
        //If $args is a string, it means that
        //the container should use a previously
        //set dependency
        if (is_string($args)) {
            $args = $this->getDependency($name);
        }

        //Register dependency info in the container
        $this->map[$name] = [
            'className' => $className,
            'args' => $args,
            'isSingleton' => $asSingleton
        ];
    }

    /**
     * Retrieve a previously set dependency
     *
     * @param string $name
     *
     * @return object
     * @throws \Exception
     */
    private function getDependency(string $name)
    {
        //Checks if object was registered previously
        if (isset($this->map[$name])) {
            //Reads class information
            $info = $this->map['name'];

            //Creates a new instance
            if (
                !$info['isSingleton']
                || ($info['isSingleton'] && !isset($this->dependencies[$name]))
            ) {
                $this->dependencies[$name] = new $info['className'](...$info['args']);
            }

            //Returns stored object
            return $this->dependencies[$name];
        }

        //Does not have a map, can't find desired dependency
        throw new \Exception("Unknown dependency: $name");
    }

    /**
     * Container constructor.
     */
    public function __construct()
    {
        $this->dependencies = new \SplObjectStorage();
    }

    /**
     * @param $name
     *
     * @return object
     */
    public function __get($name)
    {
        return $this->getDependency($name);
    }
}