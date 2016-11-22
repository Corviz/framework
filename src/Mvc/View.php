<?php

namespace Corviz\Mvc;

use Corviz\Mvc\View\TemplateEngine;

class View
{
    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * @var string
     */
    private $file;

    /**
     * @var
     */
    private $data;

    /**
     * Draw a template using application defined
     * template engine.
     *
     * @return void
     */
    public function draw()
    {
        $this->templateEngine->draw(
            $this->file, $this->data
        );
    }

    /**
     * @param mixed $data
     */
    public function setData(array &$data)
    {
        $this->data = $data;
    }

    /**
     * @param string $file
     */
    public function setFile(string $file)
    {
        $this->file = $file;
    }

    /**
     * View constructor.
     *
     * @param TemplateEngine $engine
     */
    public function __construct(TemplateEngine $engine)
    {
        $this->templateEngine = $engine;
    }
}
