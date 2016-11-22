<?php

namespace Corviz\Mvc;

use Corviz\Application;
use Corviz\Mvc\View\TemplateEngine;

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

    /**
     * Get an class/service from the container;
     *
     * @param string $className
     *
     * @return mixed
     */
    protected function container(string $className)
    {
        return Application::current()->getContainer()->get($className);
    }

    /**
     * Outputs a view file/template.
     *
     * @param string $file
     * @param array $data
     *
     * @return View
     */
    protected function view(string $file, array &$data)
    {
        $view = new View($this->container(TemplateEngine::class));
        $view->setData($data);
        $view->setFile($file);
        return $view;
    }
}
