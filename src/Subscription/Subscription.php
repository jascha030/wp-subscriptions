<?php

namespace Jascha030\WPSI\Subscription;

use Exception;

/**
 * Class Subscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class Subscription implements Subscribable
{
    protected $active = false;

    /**
     * @throws Exception
     */
    public function subscribe()
    {
        if ($this->isActive()) {
            throw new Exception("Already subscribed"); //Todo: Make exception class.
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
}
