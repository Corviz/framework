<?php

namespace Corviz\Http;

use Closure;

abstract class Middleware
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param \Closure $next
     *
     * @return void
     */
    abstract public function handle(Closure $next);

    /**
     * @return \Corviz\Http\Request
     */
    public function getRequest() : Request
    {
        return $this->request;
    }

    /**
     * Middleware constructor.
     *
     * @param \Corviz\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
