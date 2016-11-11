<?php

namespace Corviz\Mvc;

abstract class Controller
{
    /**
     * @var array
     */
    private $middlewareList = [];

    /**
     * @return array
     */
    public function getMiddlewareList() : array
    {
        return $this->middlewareList;
    }

    /**
     * @param string|array $middleware
     */
    protected function addMiddleware($middleware)
    {
        $this->middlewareList += (array) $middleware;
    }
}
