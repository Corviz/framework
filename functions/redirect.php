<?php

use Corviz\Http\Response;

if (!function_exists('redirect')) {
    /**
     * @param string $ref
     * @param array $params
     * @param string|null $schema
     *
     * @return Response
     */
    function redirect(string $ref, array $params = [], string $schema = null) : Response
    {
        $url = url($ref, $params, $schema);

        return response(null, Response::CODE_REDIRECT_SEE_OTHER, [
            'Location' => $url
        ]);
    }
}