<?php

namespace Corviz\Http;


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
     * @var boolean
     */
    private $ajax = false;

    /**
     * @var string
     */
    private $clientIp = null;

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var string
     */
    private $method = null;

    /**
     * @var string|null
     */
    private $rawContent = null;

    /**
     * @var string
     */
    private $routeStr = null;

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

            //fill up complex object properties
            self::fillCurrentHeaders($request);
            self::fillCurrentRouteString($request);
            //TODO Determine if is ajax
            //TODO Retrieve client IP
            //TODO Capture rawBody and translate it to data array
            //TODO Inform method
            //TODO Determine if its using secure connection (HTTPS)

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
        $requestUri = $_SERVER['REQUEST_URI'];
        $scriptName = $_SERVER['SCRIPT_NAME'];

        //Extract route string from the URI
        $length = strlen(dirname($scriptName));
        $route = substr(explode('?', $requestUri)[0], $length);

        //Normalize slashes
        $route = '/'.trim(str_replace('\\', '/', $route), '/').'/';

        $request->setRouteStr($route);
    }

    /**
     * @return string
     */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return null
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return null|string
     */
    public function getRawContent()
    {
        return $this->rawContent;
    }

    /**
     * @return string
     */
    public function getRouteStr()
    {
        return $this->routeStr;
    }

    /**
     * @return boolean
     */
    public function isAjax() : boolean
    {
        return $this->ajax;
    }

    /**
     * @return boolean
     */
    public function isSecure() : boolean
    {
        return $this->secure;
    }

    /**
     * @param boolean $ajax
     */
    public function setAjax(boolean $ajax)
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
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
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
     * @param null|string $rawContent
     */
    public function setRawContent(string $rawContent = null)
    {
        $this->rawContent = $rawContent;
    }

    /**
     * @param string $routeStr
     */
    public function setRouteStr(string $routeStr)
    {
        $this->routeStr = $routeStr;
    }

    /**
     * @param boolean $secure
     */
    public function setSecure(boolean $secure)
    {
        $this->secure = $secure;
    }

}