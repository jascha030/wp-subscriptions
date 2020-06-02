<?php

namespace Jascha030\WP\Subscriptions\Factory;

use Jascha030\WP\Subscriptions\ActionSubscription;
use Jascha030\WP\Subscriptions\FilterSubscription;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\FilterProvider;
use Jascha030\WP\Subscriptions\Shared\Container\WordpressSubscriptionContainer;
use Jascha030\WP\Subscriptions\Shared\DataConfig;

/**
 * Class HookFactory
 *
 * @package Jascha030\WP\Subscriptions\Factory
 */
class HookSubscriptionFactory implements SubscriptionFactory
{
    public function create($provider, array $arguments = [])
    {
        if (! $arguments['type']) {
            throw new \Exception(static::class . ' expects argument "type"');
        }

        $subscriptions = [];
        $type          = $this->resolveSubscriptionType($arguments['type']);
        $data          = (WordpressSubscriptionContainer::getInstance())->getProviderData($provider, $arguments['type']);

        foreach ($data as $tag => $parameters) {
            $method   = is_array($parameters) ? $parameters[0] : $parameters;
            $callable = [$provider, $method];

            $priority          = (is_array($parameters)) ? $parameters[1] ?? 10 : 10;
            $acceptedArguments = (is_array($parameters)) ? $parameters[2] ?? 1 : 1;

            $subscriptions[] = new $type($tag, $callable, $priority, $acceptedArguments);
        }

        return $subscriptions;
    }

    protected function resolveSubscriptionType($providerType)
    {
        return (WordpressSubscriptionContainer::getInstance())->getDefinition(DataConfig::SUBSCRIPTION, $providerType);
    }
}