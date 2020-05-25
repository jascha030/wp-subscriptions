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
    const METHOD = 'add_filter';

    const UNSUB_METHOD = 'remove_filter';

    /**
     * FilterSubscription constructor.
     *
     * @param $tag
     * @param $callable
     * @param int $priority
     * @param int $acceptedArguments
     *
     * @throws \Jascha030\WP\Subscriptions\Exception\InvalidArgumentException
     */
    public function __construct($tag, $callable, $priority = 10, $acceptedArguments = 1)
    {
        parent::__construct($tag, $callable, $priority, $acceptedArguments);
    }
}
