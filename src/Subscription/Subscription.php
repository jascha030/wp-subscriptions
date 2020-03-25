<?php

namespace Jascha030\WPSI\Subscription;

use Jascha030\WPSI\Exception\SubscriptionException;

/**
 * Class Subscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class Subscription implements Subscribable
{
    protected $uuid;

    protected $active = false;

    public function __construct()
    {
        $this->uuid = uniqid();
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
