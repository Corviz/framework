<?php

namespace Corviz\Http\RequestParser;

use Corviz\Http\Request;

abstract class ContentTypeParser
{
    /**
     * Lists which types the parser supports.
     *
     * @var string[]
     */
    private $contentTypes = [];

    /**
     * @var Request
     */
    private $request;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function canHandle(string $type) : bool
    {
        $response = false;

        foreach ($this->contentTypes as $contentType) {
            if (stripos($type, $contentType) !== false) {
                $response = true;
                break;
            }
        }

        return $response;
    }

    /**
     * Convert a raw body string to array format.
     *
     * @return array
     */
    abstract public function getData() : array;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Gets an array of uploaded files,
     * from the request.
     *
     * @return array
     */
    abstract public function getFiles() : array;

    /**
     * @return \Corviz\Http\Request
     */
    protected function getRequest() : Request
    {
        return $this->request;
    }

    /**
     * Executed every time a new object
     * is instantiated (substitute for __construct).
     */
    abstract protected function initialize();

    /**
     * Determine which content types the current
     * parser supports.
     *
     * @param string|string[] $types
     */
    final protected function supports($types)
    {
        $this->contentTypes = (array) $types;
    }

    /**
     * DataParser constructor.
     *
     * @throws \Exception
     */
    final public function __construct()
    {
        $this->initialize();

        if (empty($this->contentTypes)) {
            throw new \Exception('A data parser must have support to at least one DataType');
        }
    }
}
