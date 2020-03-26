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

    /**
     * @throws NotCallableException
     */
    public function subscribe()
    {
        if (! is_callable($this->callable) && ! function_exists($this->callable)) {
            throw new NotCallableException("Invalid callable");
        }

        parent::subscribe();
    }
}
