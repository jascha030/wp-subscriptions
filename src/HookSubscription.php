<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\InvalidArgumentException;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;
use Jascha030\WP\Subscriptions\Shared\DefinitionConfig;

use function Jascha030\WP\Subscriptions\Shared\Container\WPSC;

/**
 * Class HookSubscription
 *
 * @package Jascha030\WP\Subscriptions
 */
class HookSubscription extends Subscription
{
    protected const CONTEXT = '';

    public static function create(SubscriptionProvider $provider, $context)
    {
        $subscriptions = [];
        $data          = WPSC()->getProviderData($provider, $context);
        $type          = WPSC()->getDefinition(DefinitionConfig::SUBSCRIPTION, $context);

        foreach ($data as $tag => $parameters) {
            $method   = is_array($parameters) ? $parameters[0] : $parameters;
            $callable = [$provider, $method];

            if (! is_callable($callable)) {
                throw new InvalidArgumentException('variable is not a valid callable');
            }

            $priority          = (is_array($parameters)) ? $parameters[1] ?? 10 : 10;
            $acceptedArguments = (is_array($parameters)) ? $parameters[2] ?? 1 : 1;

            $subscription = new $type();
            $subscription->setData(compact('tag', 'callable', 'priority', 'acceptedArguments'));
        }

        return $subscriptions;
    }

    protected function activation()
    {
        call_user_func_array('add_' . static::CONTEXT, $this->data);
    }

    protected function termination()
    {
        call_user_func_array('remove_' . static::CONTEXT, $this->data);
    }
}
