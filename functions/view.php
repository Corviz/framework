<?php

use Corviz\Mvc\View;
use Corviz\Mvc\View\TemplateEngine;

if (!function_exists('view')) {
    /**
     * @param string $template
     * @param array $data
     *
     * @return View
     */
    function view(string $template, array $data = []) : View
    {
        $view = new View($this->container(TemplateEngine::class));
        $view->setData($data);
        $view->setTemplateName($template);

        return $view;
    }
}