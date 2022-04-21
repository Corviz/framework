<?php

use Corviz\Application;
use Corviz\Mvc\View;
use Corviz\Mvc\View\TemplateEngine;

if (!function_exists('view')) {
    /**
     * @param string $template
     * @param array  $data
     *
     * @return View
     */
    function view(string $template, array $data = []): View
    {
        $container = Application::current()->getContainer();
        $view = new View($container->get(TemplateEngine::class));
        $view->setData($data);
        $view->setTemplateName($template);

        return $view;
    }
}
