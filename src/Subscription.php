<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\SubscriptionException;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;

/**
 * Class Subscription
 *
 * @package Jascha030\WP\Subscriptions
 */
abstract class Subscription implements Subscribable
{
    private static $constructorToken = null;

    protected $data;

    private $id;

    private $active = false;

    final private function __construct()
    {
        $this->id = uniqid();
    }

    abstract public static function create(SubscriptionProvider $provider, $context);

    final protected static function getConstructorToken()
    {
        if (self::$constructorToken === null) {
            self::$constructorToken = new \stdClass();
        }

        return self::$constructorToken;
    }

    final public function getId(): string
    {
        return $this->id;
    }

    final public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @throws SubscriptionException
     */
    final public function subscribe()
    {
        if ($this->getActive()) {
            throw new SubscriptionException("Already subscribed to {$this->id}");
        }

        $this->active = true;
        $this->activation();
    }

    final public function unsubscribe()
    {
        if (! $this->getActive()) {
            throw new SubscriptionException("Already unsubscribed to {$this->id}");
        }

        $this->active = false;
        $this->termination();
    }

    abstract protected function activation();

    abstract protected function termination();
}
