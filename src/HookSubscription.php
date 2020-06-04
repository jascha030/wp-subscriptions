<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\InvalidArgumentException;
use Jascha030\WP\Subscriptions\Exception\NotCallableException;
use Jascha030\WP\Subscriptions\Exception\SubscriptionException;

/**
 * Class HookSubscription
 *
 * @package Jascha030\WP\Subscriptions
 */
class HookSubscription extends Subscription
{
    const METHOD = '';

    const UNSUB_METHOD = '';

    protected $data;

    public function __construct($tag, $callable, $priority = 10, $acceptedArguments = 1)
    {
        parent::__construct();

        if (! is_callable($callable)) {
            throw new InvalidArgumentException("variable is not a valid callable");
        }

        $this->data = [
            'tag'               => $tag,
            'callable'          => $callable,
            'priority'          => $priority,
            'acceptedArguments' => $acceptedArguments
        ];
    }

    public function subscribe()
    {
        if (! is_callable($this->data['callable']) && ! function_exists($this->data['callable'])) {
            throw new NotCallableException("Invalid callable");
        }

        if (! empty(static::METHOD)) {
            call_user_func(static::METHOD, $this->data['tag'], $this->data['callable'], $this->data['priority'],
                $this->data['acceptedArguments']);
            parent::subscribe();
        }
    }

    public function unsubscribe()
    {
        if ($this->active()) {
            throw new SubscriptionException("Can't unsubscribe before subscribing");
        } else {
            call_user_func(static::UNSUB_METHOD, ...$this->data);
            $this->active = false;
        }
    }
}
