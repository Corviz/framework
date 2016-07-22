<?php

namespace Corviz\Http\RequestParser;

/**
 * Uses default PHP engine to parse data.
 * It will be called automatically when no other parser
 * was assigned
 * @package Corviz\Http\RequestParser
 */
class GenericParser extends ContentTypeParser
{

    /**
     * No matter what type of request,
     * this will parse according to the
     * native PHP handler
     * @param string $type
     * @return bool
     */
    public function canHandle(string $type) : bool
    {
        return true;
    }

    /**
     * @return mixed
     */
    protected function initialize() 
    {
        $this->supports('*/*');
    }

    /**
     * Convert a raw body string to array format
     * @return array
     */
    public function getData() : array
    {
        return !empty($_REQUEST) ? $_REQUEST : [];
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