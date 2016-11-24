<?php

namespace Corviz\Http;

class Response
{
    /*
     * Informational response codes.
     */
    const CODE_CONTINUE = 100;
    const CODE_SWITCHING_PROTOCOLS = 101;

    /*
     * Success response codes.
     */
    const CODE_SUCCESS = 200;
    const CODE_CREATED = 201;
    const CODE_ACCEPTED = 202;
    const CODE_NON_AUTORITATIVE_INFORMATION = 203;
    const CODE_NO_CONTENT = 204;
    const CODE_RESET_CONTENT = 205;
    const CODE_PARTIAL_CONTENT = 206;

    /*
     * Redirection response codes.
     */
    const CODE_MULTIPLE_CHOICES = 300;
    const CODE_MOVED_PERMANENTLY = 301;
    const CODE_FOUND = 302;
    const CODE_SEE_OTHER = 303;
    const CODE_NOT_MODIFIED = 304;
    const CODE_USE_PROXY = 305;
    const CODE_TEMPORARY_REDIRECT = 307;

    /*
     * Client error response codes.
     */
    const CODE_BAD_REQUEST = 400;
    const CODE_UNAUTHORIZED = 401;
    const CODE_FORBIDDEN = 403;
    const CODE_NOT_FOUND = 404;
    const CODE_METHOD_NOT_ALLOWED = 405;
    const CODE_NOT_ACCEPTABLE = 406;
    const CODE_PROXY_AUTHENTICATION_REQUIRED = 407;
    const CODE_REQUEST_TIMEOUT = 408;
    const CODE_CONFLICT = 409;
    const CODE_GONE = 410;
    const CODE_LENGTH_REQUIRED = 411;
    const CODE_PRECONDITION_FAILED = 412;
    const CODE_ENTITY_TOO_LARGE = 413;
    const CODE_URI_TOO_LONG = 414;
    const CODE_UNSUPPORTED_MEDIA_TYPE = 415;
    const CODE_RANGE_NOT_SATISFIABLE = 416;
    const CODE_EXPECTATION_FAILED = 417;

    /*
     * Server error response codes.
     */
    const CODE_INTERNAL_SERVER_ERROR = 500;
    const CODE_NOT_IMPLEMENTED = 501;
    const CODE_BAD_GATEWAY = 502;
    const CODE_SERVICE_UNAVAILABLE = 503;
    const CODE_GATEWAY_TIMEOUT = 504;
    const CODE_HTTP_VERSION_NOT_SUPPORTED = 505;

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
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Remove a header.
     *
     * @param string $key
     */
    public function removeHeader(string $key)
    {
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }
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
     * @throws \Exception
     */
    protected function sendHeaders()
    {
        if (headers_sent()) {
            throw new \Exception('Headers already sent');
        }

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }

    /**
     * @param string|null $body
     */
    public function setBody(string $body = null)
    {
        $this->body = $body;
    }

    /**
     * Set response http code.
     *
     * @param int $code
     */
    public function setCode(int $code)
    {
        $this->code = $code;
    }
}
