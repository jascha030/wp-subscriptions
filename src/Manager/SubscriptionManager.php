<?php

use Jascha030\WPSI\Provider\ActionProvider;
use Jascha030\WPSI\Provider\FilterProvider;
use Jascha030\WPSI\Provider\SubscriptionProvider;

/**
 * Class SubscriptionManager
 */
class SubscriptionManager
{
    const AVAILABLE_TYPES = [
        ActionProvider::class => HookSubscriber::class,
        FilterProvider::class => HookSubscriber::class,
    ];

    private $addedTypes = [];

    private $providers = [];

    private $subscriptions = [];

    /**
     * @param SubscriptionProvider $provider
     */
    public function register(SubscriptionProvider $provider)
    {
        if (!in_array($provider, $this->providers)) {
            $this->providers[] = $provider;
        }
    }

    private function createSubscriptionsFromProviderData()
    {
        foreach ($this->providers as $provider) {
            $implements = class_implements($provider);

            foreach (self::AVAILABLE_TYPES as $type => $subscriberName)
            {
                if (in_array($type, $implements)) {
                    $newSubscriptions = $subscriberName::create($provider);
                    $this->subscriptions = array_merge($this->subscriptions, $newSubscriptions);
                }
            }
        }
    }
}
