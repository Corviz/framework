<?php

namespace Corviz\Http;


use Corviz\Http\RequestParser\ContentTypeParser;
use Corviz\Http\RequestParser\GenericParser;

class Request
{

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    /**
     * @var static
     */
    private static $currentRequest = null;

    /**
     * @var array
     */
    private static $registeredParsers = [];

    /**
     * @var bool
     */
    private $ajax = false;

    /**
     * @var string
     */
    private $clientIp = null;

    /**
     * @var string
     */
    private $contentType = null;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var string
     */
    private $method = null;

    /**
     * @var ContentTypeParser
     */
    private $parser = null;

    /**
     * @var array
     */
    private $queryParams = [];

    /**
     * @var string|null
     */
    private $requestBody = null;

    /**
     * @var string
     */
    private $routeStr;

    /**
     * @var boolean
     */
    private $secure = false;

    /**
     * @return Request|static
     */
    public static function current()
    {
        if(is_null(self::$currentRequest)){
            $request = new static;

            //fill simple properties
            $request->setClientIp($_SERVER['REMOTE_ADDR']);
            $request->setMethod($_SERVER['REQUEST_METHOD']);
            $request->setQueryParams($_GET);
            $request->setRequestBody(file_get_contents('php://input'));

            //fill up complex object properties
            self::fillCurrentHeaders($request);
            self::fillCurrentRouteString($request);
            self::fillCurrentAjaxState($request);
            self::fillCurrentIsSecure($request);

            //the content type should be the last
            //property to be set
            $request->setContentType($_SERVER['CONTENT_TYPE'] ?: '');

            self::$currentRequest = $request;
        }

        return self::$currentRequest;
    }

    /**
     * @return string[]
     */
    public static function getValidMethods()
    {
        return [
            self::METHOD_GET,
            self::METHOD_POST,
            self::METHOD_PUT,
            self::METHOD_PATCH,
            self::METHOD_DELETE
        ];
    }

    /**
     * @param string $parserName
     * @throws \Exception
     */
    public static function registerParser(string $parserName)
    {
        $parser = new $parserName();

        if($parser instanceof ContentTypeParser){
            self::$registeredParsers []= $parser;
        }else{
            throw new \Exception("$parserName is not a valid parser");
        }
    }

    /**
     * Check if its an ajax call
     * @param Request $request
     */
    private static function fillCurrentAjaxState(Request $request)
    {
        $request->setAjax(
            isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
        );
    }

    /**
     * Determines if the current request was made using a secure
     * connection (HTTPS)
     * @param Request $request
     */
    private static function fillCurrentIsSecure(Request $request)
    {
        $request->setSecure(
            !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
        );
    }
    
    /**
     * Fill up the headers property with the values
     * provided by the client browser
     *
     * @param Request $request
     */
    private static function fillCurrentHeaders(Request $request)
    {
        $headers = [];

        //Native method exists?
        if(function_exists('getallheaders')){

            //try to use it
            if(($headers = getallheaders()) === false){
                $headers = [];
            }

        }

        //No header captured yet? Try to get it myself
        if(empty($headers)){

            /*
             * Based on a user note provided by joyview
             * at http://php.net/manual/en/function.getallheaders.php
             */
            foreach($_SERVER as $key => $value){
                if(substr($key, 0, 5) === 'HTTP_'){
                    $newKey = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    $headers[$newKey] = $value;
                }
            }

        }

        $request->setHeaders($headers);
    }

    /**
     * Capture the route string
     * @param Request $request
     */
    private static function fillCurrentRouteString(Request $request)
    {
        //Read data from $_SERVER array
        $requestUri = urldecode($_SERVER['REQUEST_URI']);
        $scriptName = $_SERVER['SCRIPT_NAME'];

        //Extract route string from the URI
        $length = strlen(dirname($scriptName));
        $routeAux = substr(explode('?', $requestUri)[0], $length);
        $route = '/';

        if($routeAux && $routeAux != '/'){
            $route .= trim(str_replace('\\', '/', $routeAux), '/').'/';
        }

        $request->setRouteStr($route);
    }

    /**
     * @return string
     */
    public function getClientIp() : string
    {
        return $this->clientIp ?: '';
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->parser->getData();
    }

    /**
     * @return array
     */
    public function getFiles() : array
    {
        return $this->parser->getFiles();
    }

    /**
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers ?: [];
    }

    /**
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method ?: '';
    }

    /**
     * @return array
     */
    public function getQueryParams() : array
    {
        return $this->queryParams ?: [];
    }
    
    /**
     * @return string
     */
    public function getRequestBody() : string
    {
        return $this->requestBody ?: '';
    }

    /**
     * @return string
     */
    public function getRouteStr() : string 
    {
        return $this->routeStr ?: '';
    }

    /**
     * @return bool
     */
    public function isAjax() : bool
    {
        return $this->ajax;
    }

    /**
     * @return bool
     */
    public function isSecure() : bool
    {
        return $this->secure;
    }

    /**
     * @param bool $ajax
     */
    public function setAjax(bool $ajax)
    {
        $this->ajax = $ajax;
    }

    /**
     * @param string $clientIp
     */
    public function setClientIp(string $clientIp)
    {
        $this->clientIp = $clientIp;
    }

    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType)
    {
        $this->contentType = $contentType;
        $this->selectDataParser();
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $validMethods = self::getValidMethods();

        if(!in_array($method, $validMethods)){
            throw new \InvalidArgumentException("Invalid method: $method");
        }

        $this->method = $method;
    }

    /**
     * @param array $queryParams
     */
    public function setQueryParams(array $queryParams)
    {
        $this->queryParams = $queryParams;
    }

    /**
     * @param string $requestBody
     */
    public function setRequestBody(string $requestBody)
    {
        $this->requestBody = $requestBody;
    }

    /**
     * @param string $routeStr
     */
    public function setRouteStr(string $routeStr)
    {
        $this->routeStr = $routeStr;
    }

    /**
     * @param bool $secure
     */
    public function setSecure(bool $secure)
    {
        $this->secure = $secure;
    }

    /**
     * Pick a content type parser
     * from the list
     */
    private function selectDataParser()
    {
        /* @var ContentTypeParser $selected */
        $selected = null;

        //Search trough the registered parsers
        foreach(self::$registeredParsers as $parser){
            /* @var ContentTypeParser $parser */
            if($parser->canHandle($this->getContentType())){
                $selected = $parser;
                break;
            }
        }

        //If no one was found, pick GenericParser
        if(is_null($selected)){
            $selected = new GenericParser();
        }

        $selected->setRequest($this);
        $this->parser = $selected;
    }

}