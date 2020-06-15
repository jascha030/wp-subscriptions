<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\InvalidArgumentException;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;

use function Jascha030\WP\Subscriptions\Shared\Container\WPSC;

/**
 * Class HookSubscription
 *
 * @package Jascha030\WP\Subscriptions
 */
abstract class HookSubscription extends Subscription
{
    protected const CONTEXT = '';

    /**
     * @param \Jascha030\WP\Subscriptions\Provider\SubscriptionProvider $provider
     * @param $context
     *
     * @return array
     * @throws \Jascha030\WP\Subscriptions\Exception\InvalidArgumentException
     * @throws \Exception
     */
    public static function create(SubscriptionProvider $provider, $context): array
    {
        $subscriptions = [];
        $data          = WPSC()->getProviderData($provider, $context);

        foreach ($data as $tag => $parameters) {
            if (is_array($parameters) && is_array($parameters[0])) {
                foreach ($parameters as $params) {
                    $subscriptions[] = static::createSubscriptionObject($provider, $tag, $params, $context);
                }
            } else {
                $subscriptions[] = static::createSubscriptionObject($provider, $tag, $parameters, $context);
            }
        }

        return ! empty($subscriptions) ? $subscriptions : [];
    }

    protected static function createSubscriptionObject(SubscriptionProvider $provider, $tag, $parameters, $context)
    {
        $callable = [$provider, is_array($parameters) ? $parameters[0] : $parameters];

        if (! is_callable($callable)) {
            throw new InvalidArgumentException('variable is not a valid callable');
        }

        $priority          = (is_array($parameters)) ? $parameters[1] ?? 10 : 10;
        $acceptedArguments = (is_array($parameters)) ? $parameters[2] ?? 1 : 1;

        $subscription = new $context();
        $subscription->setData(compact('tag', 'callable', 'priority', 'acceptedArguments'));

        return $subscription;
    }

    protected function activation(): void
    {
        call_user_func('add_' . static::CONTEXT, ...array_values($this->data));
    }

    protected function termination(): void
    {
        call_user_func('remove_' . static::CONTEXT, ...$this->data);
    }
}
