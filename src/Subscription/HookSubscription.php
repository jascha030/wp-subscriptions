<?php

namespace Jascha030\WPSI\Subscription;

use Exception;

/**
 * Class HookSubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class HookSubscription extends Hookable implements Subscribable, Unsubscribable
{
    protected $tag;

    protected $callable;

    protected $priority;

    protected $acceptedArguments;

    /**
     * HookSubscription constructor.
     *
     * @param $tag
     * @param $callable
     * @param int $priority
     * @param int $acceptedArguments
     *
     * @throws Exception
     */
    public function __construct($tag, $callable, $priority = 10, $acceptedArguments = 1)
    {
        if (! is_callable($callable)) {
            throw new Exception("variable is not a valid callable");
        }

        $this->tag = $tag;

        $this->callable = $callable;

        $this->priority = $priority;

        $this->acceptedArguments = $acceptedArguments;
    }

    /**
     * @return string
     */
    public function subscribe()
    {
        return '';
    }

    /**
     * @return string
     */
    public function unsubscribe()
    {
        return '';
    }
}
