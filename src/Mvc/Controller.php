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
     * @param string $templateName
     * @param array $data
     *
     * @return View
     */
    protected function view(string $templateName, array &$data = [])
    {
        $file = Application::current()->getDirectory();
        $file .= "views/$templateName.phtml";

        $view = new View($this->container(TemplateEngine::class));
        $view->setData($data);
        $view->setFile($file);

        return $view;
    }
}
