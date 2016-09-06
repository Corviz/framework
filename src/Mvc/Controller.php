<?php

namespace Corviz\Mvc;

use Corviz\DI\Container;

class Controller
{
    /**
     * @var Container
     */
    private $diContainer;

    /**
     * @return \Corviz\DI\Container
     */
    protected function getContainer() : Container
    {
        return $this->diContainer;
    }

    /**
     * Controller constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->diContainer = $container;
    }

    /**
     * @param $name
     *
     * @return object
     */
    public function __get($name)
    {
        return $this->diContainer->{$name};
    }
}
