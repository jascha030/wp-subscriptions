<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\InvalidArgumentException;
use Jascha030\WP\Subscriptions\Exception\NotCallableException;
use Jascha030\WP\Subscriptions\Exception\SubscriptionException;

/**
 * Class FilterSubscription
 *
 * @package Jascha030\WP\Subscriptions
 */
class FilterSubscription extends HookSubscription implements Unsubscribable
{
    /**
     * @var int
     */
    private $priority;

    /**
     * @var int
     */
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
     * @throws SubscriptionException
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
