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
    public const ID_PREFIX = 'wpsc_';

    private static $constructorToken;

    protected $data;

    private $id;

    private $active = false;

    final protected function __construct()
    {
        $this->id = uniqid(static::ID_PREFIX, true);
    }

    abstract public static function create(SubscriptionProvider $provider, $context);

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
    final public function subscribe(): void
    {
        if ($this->getActive()) {
            throw new SubscriptionException("Already subscribed to {$this->id}");
        }

        $this->active = true;
        $this->activation();
    }

    /**
     * @throws \Jascha030\WP\Subscriptions\Exception\SubscriptionException
     */
    final public function unsubscribe(): void
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
