<?php

use Corviz\Http\Request;
use Corviz\Routing\Map;
use Corviz\String\ParametrizedString;
use Corviz\String\StringUtils;

//use function Corviz\Mvc\;

if (!function_exists('url')) {
    /**
     * @param string      $ref
     * @param array       $params
     * @param string|null $schema
     *
     * @return string
     */
    function url(string $ref, array $params = [], string $schema = null): string
    {
        $link = null;
        $getBaseUrl = function () use ($schema) {

            //Guess url schema, in case it was not informed
            if (!$schema) {
                $schema = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
            }

            $defaultPort = ($schema == 'https') ? 443 : 80;

            //Capture complete URL
            $completeUrl = "$schema://{$_SERVER['SERVER_NAME']}";
            if (!$_SERVER['SERVER_PORT'] != $defaultPort) {
                $completeUrl .= ":{$_SERVER['SERVER_PORT']}";
            }
            $completeUrl .= $_SERVER['REQUEST_URI'];

            $routeStr = Request::current()->getRouteStr();

            //clear params from complete url
            $paramsPos = strpos($completeUrl, '?');
            if ($paramsPos !== false) {
                $completeUrl = substr($completeUrl, 0, $paramsPos);
            }

            //clear anchor
            $anchorPos = strpos($completeUrl, '#');
            if ($anchorPos !== false) {
                $completeUrl = substr($completeUrl, 0, $anchorPos);
            }

            //clear route
            $trimmedCompUrl = rtrim($completeUrl, '/');
            $trimmedRouteStr = rtrim($routeStr, '/');

            $routePos = $trimmedCompUrl && $trimmedRouteStr ?
                strpos($trimmedCompUrl, $trimmedRouteStr) : false;

            if ($routePos !== false) {
                $completeUrl = substr($completeUrl, 0, $routePos);
            }

            //remove final slash
            if (StringUtils::endsWith($completeUrl, '/')) {
                $completeUrl = substr($completeUrl, 0, -1);
            }

            return $completeUrl;
        };

        //Is alias?
        $route = Map::getRouteByAlias($ref);
        if ($route) {
            $link = $getBaseUrl().$route;
        } elseif (StringUtils::startsWith($ref, '/')) {
            //Is a route?
            $link = $getBaseUrl().$ref;
        } else {
            //Neither route or alias;
            $link = $ref;
        }

        //Parse
        $pString = new ParametrizedString($link);
        $link = $pString->parse($params);

        foreach ($pString->getParameters() as $parameterName) {
            unset($params[$parameterName]);
        }

        //Add remaining params
        if (!empty($params)) {
            $httpQuery = http_build_query($params);
            $link .= strpos($link, '?') === false ? '?' : '&';
            $link .= $httpQuery;
        }

        return $link;
    }
}
