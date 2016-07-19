<?php

namespace Corviz\Http\RequestParser;

/**
 * Provided 'application/json' parser
 * @package Corviz\Http\RequestParser
 */
class JsonParser extends ContentTypeParser
{

    /**
     * Executed every time a new object
     * is instantiated (substitute for __construct)
     */
    protected function initialize()
    {
        $this->supports([
            'application/json',
            'application/x-javascript',
            'text/javascript',
            'text/x-javascript',
            'text/x-json'
        ]);
    }

    /**
     * Convert a raw body string to array format
     * @return array
     */
    public function getData() : array
    {
        $data = json_decode($this->getRequest()->getRequestBody(), true);
        return $data;
    }

    /**
     * Gets an array of uploaded files,
     * from the request
     * @return array
     */
    public function getFiles() : array
    {
        return [];
    }

}