<?php

namespace Corviz\Http;

use Closure;

abstract class Middleware
{
    /**
     * @param \Closure $next
     *
     * @return void
     */
    abstract public function handle(Closure $next);
}
