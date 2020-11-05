<?php

namespace Corviz\Events;

use Corviz\Behaviour\Observable;
use Corviz\Behaviour\Observer;

abstract class Event implements Observable
{
    /**
     * @var array
     */
    private static $handlers = [];

    /**
     * @var bool
     */
    private $cancelled = false;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    public static function register(Observer $observer)
    {
        if (!$observer instanceof EventHandler) {
            throw new \Exception('$observer must be an instance of EventHandler');
        }

        self::$handlers[] = $observer;
    }

    /**
     * Stop the execution from the next
     * handlers.
     */
    public function cancel()
    {
        if (!$this->isCancelable()) {
            return;
        }

        $this->cancelled = true;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isCancelable()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->cancelled;
    }

    /**
     * {@inheritdoc}
     */
    public function notifyObservers(array $data = [])
    {
        /* @var $handler EventHandler */
        foreach (self::$handlers as $handler) {
            //Execute
            $data['data'] = $this->getData();
            $handler->notify($this, $data);

            //Check if this was canceled
            if ($this->isCancelled()) {
                break;
            }
        }

        $this->cancelled = false;
    }

    /**
     * Event constructor.
     *
     * @param string $type
     * @param array  $data
     */
    public function __construct(string $type, array $data = [])
    {
        $this->type = $type;
        $this->data = $data;
    }
}
