<?php

namespace Jascha030\WP\Subscriptions\Shared;

use Jascha030\WP\Subscriptions\ActionSubscription;
use Jascha030\WP\Subscriptions\Factory\HookSubscriptionFactory;
use Jascha030\WP\Subscriptions\FilterSubscription;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\FilterProvider;
use Jascha030\WP\Subscriptions\Provider\ShortcodeProvider;

class DefinitionConfig
{
    const SUBSCRIPTION = 0;
    const PROPERTY = 1;
    const CREATION_METHOD = 2;

    const PREDEFINED_PROVIDER_SUBSCRIPTION_TYPES = [
        ActionProvider::class => ActionSubscription::class,
        FilterProvider::class => FilterSubscription::class
    ];

    const PREDEFINED_PROVIDER_DATA_PROPERTIES = [
        ActionProvider::class    => 'actions',
        FilterProvider::class    => 'filters',
        ShortcodeProvider::class => 'shortcodes'
    ];

    const PREDEFINED_CREATION_METHODS = [
        ActionProvider::class => HookSubscriptionFactory::class,
        FilterProvider::class => HookSubscriptionFactory::class
    ];

    private $definitions = [];

    public function __construct()
    {
        $this->definitions['subscriptionTypes']      = self::PREDEFINED_PROVIDER_SUBSCRIPTION_TYPES;
        $this->definitions['providerDataProperties'] = self::PREDEFINED_PROVIDER_DATA_PROPERTIES;
        $this->definitions['creationMethods']        = self::PREDEFINED_CREATION_METHODS;
    }

    public function getDefinition(int $type, string $key)
    {
        switch ($type) {
            case DefinitionConfig::SUBSCRIPTION:
                return $this->definitions['subscriptionTypes'][$key] ?? false;
                break;
            case DefinitionConfig::PROPERTY:
                return $this->definitions['providerDataProperties'][$key] ?? false;
                break;
            case DefinitionConfig::CREATION_METHOD:
                return $this->definitions['providerMethods'][$key] ?? false;
                break;
        }

        throw new \Exception("Unknown definition");
    }

    /**
     * @return array|string[]
     */
    public function getSubscriptionTypes(): array
    {
        return $this->definitions['subscriptionTypes'];
    }

    /**
     * @return array|string[]
     */
    public function getProviderDataProperties()
    {
        return $this->definitions['providerDataProperties'];
    }

    /**
     * @return array|string[]
     */
    public function getProviderMethods()
    {
        return $this->definitions['providerMethods'];
    }

    public function registerSubscriptionType(string $providerClass, string $dataClass, $creationMethod = null)
    {
        /**
         * Todo: implement
         *
         * Need provider class
         * Need data / subscription class
         * Need creation method in form of Factory class / Callable / null = dataclass without constructor
         */
    }
}