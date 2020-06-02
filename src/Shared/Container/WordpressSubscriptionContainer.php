<?php

namespace Jascha030\WP\Subscriptions\Shared\Container;

use Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException;
use Jascha030\WP\Subscriptions\Factory\HookSubscriptionFactory;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\FilterProvider;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;

class WordpressSubscriptionContainer extends Container
{
    protected $providerMethods = [
        ActionProvider::class => HookSubscriptionFactory::class,
        FilterProvider::class => HookSubscriptionFactory::class,
        //        ShortcodeProvider::class => ShortcodeSubscription::class
    ];

    protected $subscriptions = [];

    protected $failed = [];

    public function run()
    {
        $this->createSubscriptionsFromProvidedData();

        foreach ($this->subscriptions as &$subscription) {
            try {
                $subscription->subscribe();
            } catch (\Exception $exception) {
                $this->failed[$subscription->getUuid()] = $exception->getMessage();
            }
        }
    }

    public function register($abstract, $provider = null)
    {
        if (is_object($abstract) && ! $provider) {
            $provider = $abstract;
            $abstract = get_class($provider);
        }

        if (! is_subclass_of($abstract, SubscriptionProvider::class)) {
            throw new DoesNotImplementProviderException();
        }

        if (! $this->bound($abstract)) {
            $this->bind($abstract, $provider);
        }
    }

    protected function createSubscriptionsFromProvidedData()
    {
        foreach ($this->bindings as $abstract => $provider) {
            if (! is_object($provider)) {
                $provider = $abstract;
            }
            foreach ($this->providerMethods as $type => $factory) {
                $newSubscriptions    = $this->createSubscriptions($abstract, $type);
                $this->subscriptions = array_merge($this->subscriptions, $newSubscriptions);
            }
        }
    }

    protected function createSubscriptions($provider, $type)
    {
        if (! array_key_exists($type, $this->providerMethods)) {
            return;
        }

        $factory = $this->providerMethods[$type];
        if (! $this->bound($factory)) {
            $this->bind($factory);
        }

        $provider = $this->resolve($provider);

        return ($this->resolve($factory))->create($provider, ['type' => $type]);
    }
}