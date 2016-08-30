<?php

namespace Corviz\Mvc;

use Corviz\DI\Container;

class Controller
{
    /**
     * @var Container
     */
    private $diContainer;

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
     * @param string $name
     */
    public function __get($name)
    {
        return $this->diContainer->{$name};
    }
}
