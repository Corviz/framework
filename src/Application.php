<?php

namespace Corviz;

use Corviz\Behaviour\Runnable;

/**
 * Main app controller
 *
 * @package Corviz
 */
class Application implements Runnable
{
    /**
     * @var string
     */
    private $directory;

    public function run(...$args)
    {

    }

    /**
     * Application constructor.
     *
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }
}
