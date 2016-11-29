<?php

namespace Corviz\Http;

use Corviz\Behaviour\ConvertsToJson;
use Corviz\Mvc\View;

class ResponseFactory
{
    /**
     * @param mixed $input
     *
     * @return Response
     */
    public static function createResponse($input = null) : Response
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

        //Array
        if (is_array($input)) {
            self::createFromArray($response, $input);
        }

        //Json
        if ($input instanceof ConvertsToJson) {
            self::createFromJsonable($response, $input);
        }

        return $response;
    }

    /**
     * @param \Corviz\Http\Response $response
     * @param array                 $input
     */
    private static function createFromArray(Response $response, array $input)
    {
        $response->setBody(json_encode($input));
        self::setJsonHeader($response);
    }

    /**
     * @param \Corviz\Http\Response            $response
     * @param \Corviz\Behaviour\ConvertsToJson $input
     */
    private static function createFromJsonable(Response $response, ConvertsToJson $input)
    {
        $response->setBody($input->toJson());
        self::setJsonHeader($response);
    }

    /**
     * @param \Corviz\Http\Response $response
     */
    private static function setJsonHeader(Response $response)
    {
        $response->addHeader('Content-type', 'application/json');
    }
}
