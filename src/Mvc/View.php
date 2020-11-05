<?php

namespace Corviz\Mvc;

use Corviz\Application;
use Corviz\Mvc\View\TemplateEngine;
use Exception;

class View
{
    /**
     * @var string
     */
    private static $extension = 'phtml';

    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var array
     */
    private $data;

    /**
     * @param string $extension
     */
    public static function setExtension(string $extension)
    {
        self::$extension = $extension;
    }

    /**
     * Draw a template using application defined
     * template engine.
     *
     * @throws \Exception
     *
     * @return string
     */
    public function draw()
    {
        $file = Application::current()->getDirectory();
        $file .= "views/{$this->templateName}.";
        $file .= self::$extension;

        if (!file_exists($file)) {
            throw new Exception("Template file not found: $file");
        }

        return $this->templateEngine->draw(
            $file,
            $this->data
        );
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param string $templateName
     */
    public function setTemplateName(string $templateName)
    {
        $this->templateName = $templateName;
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
