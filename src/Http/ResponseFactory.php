<?php

namespace Corviz\Http;

use Corviz\Behaviour\ConvertsToJson;
use Corviz\Mvc\View;

class ResponseFactory
{
    /**
     * Create a response object from given $input.
     *
     * @param mixed $input
     *
     * @return Response
     */
    public static function build($input = null): Response
    {
        //Is a response object already
        if ($input instanceof Response) {
            return $input;
        }

        $response = new Response();

        //View
        if ($input instanceof View) {
            $response->setBody($input->draw());
        }

        //Json
        if ($input instanceof ConvertsToJson) {
            self::createFromJsonable($response, $input);
        }

        //Array
        if (is_array($input)) {
            self::createFromArray($response, $input);
        }

        //String
        if (is_string($input)) {
            $response->setBody($input);
        }

        return $response;
    }

    /**
     * @param Response $response
     * @param array    $input
     */
    private static function createFromArray(Response $response, array $input)
    {
        $response->setBody(json_encode($input));
        self::setJsonHeader($response);
    }

    /**
     * @param Response       $response
     * @param ConvertsToJson $input
     */
    private static function createFromJsonable(Response $response, ConvertsToJson $input)
    {
        $response->setBody($input->toJson());
        self::setJsonHeader($response);
    }

    /**
     * @param Response $response
     */
    private static function setJsonHeader(Response $response)
    {
        $response->addHeader('Content-type', 'application/json');
    }
}
