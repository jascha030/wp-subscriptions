<?php

namespace Jascha030\WP\Subscriptions\Shared;

use Jascha030\WP\Subscriptions\ActionSubscription;
use Jascha030\WP\Subscriptions\Factory\HookSubscriptionFactory;
use Jascha030\WP\Subscriptions\FilterSubscription;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\FilterProvider;
use Jascha030\WP\Subscriptions\Provider\ShortcodeProvider;

class DataConfig
{
    const SUBSCRIPTION = 0;
    const PROPERTY = 1;
    const CREATION_METHOD = 2;

    const PREDEFINED_PROVIDER_SUBSCRIPTION_TYPES = [
        ActionProvider::class => ActionSubscription::class,
        FilterProvider::class => FilterSubscription::class
    ];

    const PREDEFINED_PROVIDER_DATA_TYPES = [
        ActionProvider::class    => 'actions',
        FilterProvider::class    => 'filters',
        ShortcodeProvider::class => 'shortcodes'
    ];

    const PREDEFINED_PROVIDER_DATA_METHODS = [
        ActionProvider::class => HookSubscriptionFactory::class,
        FilterProvider::class => HookSubscriptionFactory::class
    ];

    protected $providerDataProperties = [];

    protected $providerMethods = [];

    protected $subscriptionTypes = [];

    public function __construct()
    {
        $this->subscriptionTypes      = self::PREDEFINED_PROVIDER_SUBSCRIPTION_TYPES;
        $this->providerDataProperties = self::PREDEFINED_PROVIDER_DATA_TYPES;
        $this->providerMethods        = self::PREDEFINED_PROVIDER_DATA_METHODS;
    }

    /**
     * @return array|string[]
     */
    public function getSubscriptionTypes(): array
    {
        return $this->subscriptionTypes;
    }

    /**
     * @return array|string[]
     */
    public function getProviderDataProperties()
    {
        return $this->providerDataProperties;
    }

    /**
     * @return array|string[]
     */
    public function getProviderMethods()
    {
        return $this->providerMethods;
    }
}