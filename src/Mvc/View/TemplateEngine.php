<?php

namespace Corviz\Mvc\View;

interface TemplateEngine
{
    /**
     * Proccess and render a template.
     *
     * @param string $file
     * @param array  $data
     *
     * @return void
     */
    public function draw(string $file, array $data);
}
