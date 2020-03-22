<?php

namespace Jascha030\WPSI\Subscription;

/**
 * Class Subscription
 *
 * @package Jascha030\WPSI\Subscription
 */
abstract class Subscription implements Subscribable
{
    protected $active = false;

    abstract public function subscribe();

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }
}
