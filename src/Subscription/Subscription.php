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
    protected $uuid;

    protected $active = false;

    public function __construct()
    {
        $this->uuid = uniqid();
    }

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

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }
}
