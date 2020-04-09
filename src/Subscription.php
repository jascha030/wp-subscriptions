<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\SubscriptionException;

/**
 * Class Subscription
 *
 * @package Jascha030\WP\Subscriptions
 */
class Subscription implements Subscribable
{
    protected $uuid;

    protected $active = false;

    public function __construct()
    {
        $this->uuid = uniqid();
    }

    public function info()
    {
        return get_class($this);
    }

    /**
     * @throws SubscriptionException
     */
    public function subscribe()
    {
        if ($this->isActive()) {
            throw new SubscriptionException("Already subscribed");
        } else {
            $this->active = true;
        }
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
