<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\InvalidArgumentException;
use Jascha030\WP\Subscriptions\Exception\NotCallableException;
use Jascha030\WP\Subscriptions\Exception\SubscriptionException;

/**
 * Class ActionHookSubscription
 *
 * @package Jascha030\WP\Subscriptions
 */
class ActionSubscription extends HookSubscription implements Unsubscribable
{
    private $priority;

    private $acceptedArguments;

    /**
     * ActionSubscription constructor.
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

        add_action($this->tag, $this->callable, $this->priority, $this->acceptedArguments);
    }

    /**
     * @throws SubscriptionException
     */
    public function unsubscribe()
    {
        if ($this->isActive()) {
            throw new SubscriptionException("Can't unsubscribe before subscribing");
        } else {
            remove_action($this->tag, $this->callable, $this->priority, $this->acceptedArguments);
            $this->active = false;
        }
    }
}
