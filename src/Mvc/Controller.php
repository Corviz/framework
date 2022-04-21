<?php

namespace Corviz\Mvc;

use Corviz\Application;
use Corviz\Http\Response;

abstract class Controller
{
    /**
     * @var array
     */
    private $middlewareList = [];

    /**
     * @return array
     */
    public function getMiddlewareList(): array
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
     * Get an class/service from the container;.
     *
     * @param string $className
     *
     * @throws \Exception
     *
     * @return mixed
     */
    protected function container(string $className)
    {
        return Application::current()->getContainer()->get($className);
    }

    /**
     * @param string $ref
     * @param array  $params
     * @param string $schema
     *
     * @return string
     */
    protected function link(string $ref, array $params = [], string $schema = null): string
    {
        return url($ref, $params, $schema);
    }

    /**
     * Creates a redirect response.
     *
     * @param string      $ref
     * @param array       $params
     * @param string|null $schema
     *
     * @return Response
     */
    protected function redirect(string $ref, array $params = [], string $schema = null): Response
    {
        return redirect($ref, $params, $schema);
    }

    /**
     * Outputs a view file/template.
     *
     * @param string $templateName
     * @param array  $data
     *
     * @throws \Exception
     *
     * @return View
     */
    protected function view(string $templateName, array $data = []): View
    {
        return view($templateName, $data);
    }
}
