<?php

namespace Corviz\DI;

use Corviz\Application;

abstract class Provider
{
    /**
     * @var \Corviz\Application
     */
    private $application;

    /**
     * Init dependencies in the application container.
     */
    public function register()
    {
        return;
    }

    /**
     * @param string $conf
     *
     * @return mixed
     */
    final protected function config(string $conf)
    {
        return $this->application->config($conf);
    }

    /**
     * Get current application container.
     *
     * @return \Corviz\DI\Container
     */
    final protected function container()
    {
        return $this->application->getContainer();
    }

    /**
     * Provider constructor.
     *
     * @param \Corviz\Application $application
     */
    final public function __construct(Application $application)
    {
        $this->application = $application;
    }
}