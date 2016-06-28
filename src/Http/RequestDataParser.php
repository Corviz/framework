<?php

namespace Corviz\Http;


abstract class RequestDataParser
{

    /**
     * Lists which types the parser supports
     * @var string[]
     */
    private $contentTypes = [];

    /**
     * @param string $type
     * @return boolean
     */
    public function canHandle(string $type) : boolean
    {
        $response = false;

        foreach($this->contentTypes as $contentType){
            if(stripos($type, $contentType) !== false){
                $response = true;
                break;
            }
        }

        return $response;
    }

    /**
     * @return mixed
     */
    protected abstract function initialize() : void;

    /**
     * Retrieve the raw body content in a request
     * @return string
     */
    protected function getRawBody() : string
    {
        return file_get_contents('php://input');
    }

    /**
     * Convert a raw body string to array format
     * @param string $body
     * @return array
     */
    protected abstract function parseData(string $body) : array;

    /**
     * Determine which content types the current
     * parser supports
     * @param string|string[] $types
     */
    protected final function supports($types)
    {
        $this->contentTypes = (array) $types;
    }

    /**
     * DataParser constructor.
     * @throws \Exception
     */
    public final function __construct()
    {
        $this->initialize();

        if(empty($this->contentTypes)){
            throw new \Exception("A data parser must have support to at least one DataType");
        }
    }

}