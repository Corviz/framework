<?php

namespace Corviz;

use Corviz\Behaviour\Runnable;
use Corviz\DI\Container;

/**
 * Main app controller.
 */
class Application implements Runnable
{
    /**
     * @var Application
     */
    private static $current;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var string
     */
    private $directory;

    /**
     * Get the current running application.
     *
     * @return \Corviz\Application
     */
    public static function current() : Application
    {
        return self::$current;
    }

    /**
     * @return \Corviz\DI\Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * {@inheritdoc}
     */
    public function run(...$args)
    {
        self::$current = $this;
    }

    /**
     * Application constructor.
     *
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
        $this->container = new Container();
    }
}
