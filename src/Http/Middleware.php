<?php

namespace Corviz\Http;

use Closure;

abstract class Middleware
{
    /**
     * @param \Closure $next
     *
     * @return Response
     */
    abstract public function handle(Closure $next): Response;
}
