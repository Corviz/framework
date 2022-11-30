<?php

namespace Corviz\Http;

use Exception;

class Response
{
    /*
     * Informational response codes.
     */
    const CODE_INFO_CONTINUE = 100;
    const CODE_INFO_SWITCHING_PROTOCOLS = 101;

    /*
     * Success response codes.
     */
    const CODE_SUCCESS = 200;
    const CODE_SUCCESS_CREATED = 201;
    const CODE_SUCCESS_ACCEPTED = 202;
    const CODE_SUCCESS_NON_AUTORITATIVE_INFORMATION = 203;
    const CODE_SUCCESS_NO_CONTENT = 204;
    const CODE_SUCCESS_RESET_CONTENT = 205;
    const CODE_SUCCESS_PARTIAL_CONTENT = 206;

    /*
     * Redirection response codes.
     */
    const CODE_REDIRECT_MULTIPLE_CHOICES = 300;
    const CODE_REDIRECT_MOVED_PERMANENTLY = 301;
    const CODE_REDIRECT_FOUND = 302;
    const CODE_REDIRECT_SEE_OTHER = 303;
    const CODE_REDIRECT_NOT_MODIFIED = 304;
    const CODE_REDIRECT_USE_PROXY = 305;
    const CODE_REDIRECT_TEMPORARY_REDIRECT = 307;

    /*
     * Client error response codes.
     */
    const CODE_ERROR_BAD_REQUEST = 400;
    const CODE_ERROR_UNAUTHORIZED = 401;
    const CODE_ERROR_FORBIDDEN = 403;
    const CODE_ERROR_NOT_FOUND = 404;
    const CODE_ERROR_METHOD_NOT_ALLOWED = 405;
    const CODE_ERROR_NOT_ACCEPTABLE = 406;
    const CODE_ERROR_PROXY_AUTHENTICATION_REQUIRED = 407;
    const CODE_ERROR_REQUEST_TIMEOUT = 408;
    const CODE_ERROR_CONFLICT = 409;
    const CODE_ERROR_GONE = 410;
    const CODE_ERROR_LENGTH_REQUIRED = 411;
    const CODE_ERROR_PRECONDITION_FAILED = 412;
    const CODE_ERROR_ENTITY_TOO_LARGE = 413;
    const CODE_ERROR_URI_TOO_LONG = 414;
    const CODE_ERROR_UNSUPPORTED_MEDIA_TYPE = 415;
    const CODE_ERROR_RANGE_NOT_SATISFIABLE = 416;
    const CODE_ERROR_EXPECTATION_FAILED = 417;

    /*
     * Server error response codes.
     */
    const CODE_ERROR_SERVER_INTERNAL_SERVER_ERROR = 500;
    const CODE_ERROR_SERVER_NOT_IMPLEMENTED = 501;
    const CODE_ERROR_SERVER_BAD_GATEWAY = 502;
    const CODE_ERROR_SERVER_SERVICE_UNAVAILABLE = 503;
    const CODE_ERROR_SERVER_GATEWAY_TIMEOUT = 504;
    const CODE_ERROR_SERVER_HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * @var int
     */
    private $code = self::CODE_SUCCESS;

    /**
     * @var null
     */
    private $body = null;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * Add header.
     *
     * @param string $key
     * @param string $value
     *
     * @return $this;
     */
    public function addHeader($key, $value): Response
    {
        $this->headers[$key] = $value;

        return $this;
    }

    /**
     * Builds a json response.
     *
     * @param $data
     *
     * @return $this
     */
    public function json($data): Response
    {
        $json = json_encode($data);

        if ($json === false) {
            throw new Exception('Could not convert data to json!');
        }

        $this->addHeader('Content-Type', 'application/json');
        $this->setBody($json);

        return $this;
    }

    /**
     * Remove a header.
     *
     * @param string $key
     *
     * @return $this
     */
    public function removeHeader(string $key): Response
    {
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }

        return $this;
    }

    /**
     * Send response to client.
     *
     * @return void
     */
    public function send()
    {
        http_response_code($this->code);
        $this->sendHeaders();
        $this->sendBody();
    }

    /**
     * Outputs response body.
     */
    protected function sendBody()
    {
        echo $this->body;
    }

    /**
     * Sends header to the client through 'header'
     * php function.
     *
     * @throws Exception
     */
    protected function sendHeaders()
    {
        if (headers_sent()) {
            throw new Exception('Headers already sent');
        }

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }

    /**
     * @param string|null $body
     *
     * @return $this
     */
    public function setBody(string $body = null): Response
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Set response http code.
     *
     * @param int $code
     *
     * @return $this
     */
    public function setCode(int $code): Response
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param string $contents
     *
     * @return $this
     */
    public function write(string $contents): Response
    {
        $this->body .= $contents;

        return $this;
    }
}
