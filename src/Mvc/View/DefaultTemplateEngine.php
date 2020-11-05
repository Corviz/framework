<?php

namespace Corviz\Mvc\View;

class DefaultTemplateEngine implements TemplateEngine
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $file;

    /**
     * Proccess and render a template.
     *
     * @param string $file
     * @param array  $data
     *
     * @return string
     */
    public function draw(string $file, array $data): string
    {
        $this->file = $file;
        $this->data = $data;

        return $this->getOutputs();
    }

    /**
     * Handle template file.
     *
     * @return string
     */
    private function getOutputs(): string
    {
        ob_start();
        extract($this->data);
        require $this->file;
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }
}
