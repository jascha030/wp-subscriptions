<?php

namespace Jascha030\WP\Subscriptions\Factory;

use Jascha030\WP\Subscriptions\ActionSubscription;
use Jascha030\WP\Subscriptions\FilterSubscription;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\FilterProvider;

/**
 * Class HookFactory
 *
 * @package Jascha030\WP\Subscriptions\Factory
 */
class HookSubscriptionFactory implements SubscriptionFactory
{
    const SUBSCRIPTION_TYPES = [
        ActionProvider::class => ActionSubscription::class,
        FilterProvider::class => FilterSubscription::class
    ];

    public function create($provider, array $arguments = [])
    {
        if (! $arguments['type']) {
            throw new \Exception(static::class . ' expects argument "type"');
        }

        $subscriptions = [];
        $type          = $this->resolveSubscriptionType($arguments['type']);

        $data = $provider->getData($arguments['type']);

        foreach ($provider->getData($arguments['type']) as $tag => $parameters) {
            $method   = is_array($parameters) ? $parameters[0] : $parameters;
            $callable = [$provider, $method];

            $priority          = (is_array($parameters)) ? $parameters[1] ?? 10 : 10;
            $acceptedArguments = (is_array($parameters)) ? $parameters[2] ?? 1 : 1;

            $subscriptions[]   = new $type($tag, $callable, $priority, $acceptedArguments);
        }

        return $subscriptions;
    }

    protected function resolveSubscriptionType($providerType)
    {
        return self::SUBSCRIPTION_TYPES[$providerType] ?? false;
    }
}