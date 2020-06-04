<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\SubscriptionException;

/**
 * Class Subscription
 *
 * @package Jascha030\WP\Subscriptions
 */
abstract class Subscription implements Subscribable
{
    protected $id;

    protected $active = false;

    final public function __construct()
    {
        $this->id = uniqid();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @throws SubscriptionException
     */
    public function subscribe()
    {
        if ($this->active()) {
            throw new SubscriptionException("Already subscribed");
        }

        $this->active = true;
    }

    public function active(): bool
    {
        return $this->active;
    }
}
