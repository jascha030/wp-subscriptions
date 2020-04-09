<?php

namespace Jascha030\WPOL\Subscription;

use Jascha030\WPOL\Subscription\Exception\InvalidArgumentException;
use Jascha030\WPOL\Subscription\Exception\NotCallableException;

/**
 * Class HookSubscription
 *
 * @package Jascha030\WPOL\Subscription
 */
class HookSubscription extends Subscription
{
    protected $tag;

    protected $callable;

    /**
     * HookSubscription constructor.
     *
     * @param $tag
     * @param $callable
     *
     * @throws InvalidArgumentException
     */
    public function __construct($tag, $callable)
    {
        parent::__construct();

        if (! is_callable($callable)) {
            throw new InvalidArgumentException("variable is not a valid callable");
        }

        $this->tag = $tag;

        $this->callable = $callable;
    }

    public function info()
    {
        $methodString = null;

        if ((is_array($this->callable))) {
            $methodString = (is_string($this->callable[0])) ? $this->callable[0] : get_class($this->callable[0]);
            $methodString .= ' ' . $this->callable[1];
        }

        return $methodString ?? $this->callable;
    }

    /**
     * @throws NotCallableException
     * @throws Exception\SubscriptionException
     */
    public function subscribe()
    {
        if (! is_callable($this->callable) && ! function_exists($this->callable)) {
            throw new NotCallableException("Invalid callable");
        }

        parent::subscribe();
    }
}
