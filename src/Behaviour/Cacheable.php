<?php


namespace Corviz\Behaviour;

interface Cacheable
{
    /**
     * Determines if the current object can
     * or can not be cached.
     *
     * @return bool
     */
    public function isCached() : bool;

    /**
     * Read the contents from the cache.
     *
     * @return mixed
     */
    public function readFromCache();

    /**
     * Store the current object in the cache
     * and returns true if the success was
     * complete successfully.
     * Otherwise, returns false
     *
     * @return bool
     */
    public function storeInCache() : bool;
}