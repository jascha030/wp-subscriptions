<?php

namespace Jascha030\WPOL\Subscription;

use Jascha030\WPOL\Subscription\Exception\InvalidArgumentException;
use Jascha030\WPOL\Subscription\Exception\NotCallableException;
use Jascha030\WPOL\Subscription\Exception\SubscriptionException;

/**
 * Class FilterSubscription
 *
 * @package Jascha030\WPOL\Subscription
 */
class FilterSubscription extends HookSubscription implements Unsubscribable
{
    private $priority;

    private $acceptedArguments;

    /**
     * FilterSubscription constructor.
     *
     * @param $tag
     * @param $callable
     * @param int $priority
     * @param int $acceptedArguments
     *
     * @throws InvalidArgumentException
     */
    public function __construct($tag, $callable, $priority = 10, $acceptedArguments = 1)
    {
        parent::__construct($tag, $callable);

        $this->priority = $priority;

        $this->acceptedArguments = $acceptedArguments;
    }

    /**
     * @throws NotCallableException
     */
    public function subscribe()
    {
        parent::subscribe();

        add_filter($this->tag, $this->callable, $this->priority, $this->acceptedArguments);
    }

    /**
     * @throws SubscriptionException
     */
    public function unsubscribe()
    {
        if ($this->isActive()) {
            throw new SubscriptionException("Can't unsubscribe before subscribing");
        } else {
            remove_filter($this->tag, $this->callable, $this->priority, $this->acceptedArguments);
        }
    }
}