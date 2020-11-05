<?php

namespace Corviz\Http\RequestParser;

use Corviz\Http\Request;

/**
 * Provided 'application/x-www-form-urlencoded' parser.
 */
class FormUrlEncodedParser extends ContentTypeParser
{
    /**
     * Executed every time a new object
     * is instantiated (substitute for __construct).
     */
    protected function initialize()
    {
        $this->supports('application/x-www-form-urlencoded');
    }

    /**
     * Convert a raw body string to array format.
     *
     * @return array
     */
    public function getData(): array
    {
        $data = [];
        $request = $this->getRequest();
        $method = $request->getMethod();

        switch ($method) {

            //GET
            case Request::METHOD_GET:
                $data = $_GET ?: [];
            break;

            //POST
            case Request::METHOD_POST:
                $data = $_POST ?: [];
            break;

            //Other supported
            case Request::METHOD_DELETE:
            case Request::METHOD_PUT:
            case Request::METHOD_PATCH:
                parse_str($request->getRequestBody(), $data);
            break;

        }

        return $data;
    }

    /**
     * Gets an array of uploaded files,
     * from the request.
     *
     * @return array
     */
    public function getFiles(): array
    {
        return [];
    }
}
