<?php

use Corviz\Http\Response;

if (!function_exists('response')) {
    /**
     * @param string $body
     * @param int $code
     * @param array $headers
     *
     * @return Response
     */
    function response(string $body = '', int $code = Response::CODE_SUCCESS, array $headers = []) : Response
    {
        $response = new Response();

        $response->setCode($code);

        if (!empty($headers)) {
            foreach ($headers as $header => $value) {
                $response->addHeader($header, $value);
            }
        }

        if ($body) {
            $response->setBody($body);
        }

        return $response;
    }
}