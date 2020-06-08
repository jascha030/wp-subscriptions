<?php

namespace Jascha030\WP\Subscriptions\Shared;

use Exception;
use Jascha030\WP\Subscriptions\ActionSubscription;
use Jascha030\WP\Subscriptions\FilterSubscription;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\FilterProvider;
use Jascha030\WP\Subscriptions\Provider\ShortcodeProvider;

class DefinitionConfig
{
    public const SUBSCRIPTION = 0;
    public const PROPERTY = 1;

    /**
     * @var \Jascha030\WP\Subscriptions\Subscription[]
     */
    public const PREDEFINED_PROVIDER_SUBSCRIPTION_TYPES = [
        ActionProvider::class => ActionSubscription::class,
        FilterProvider::class => FilterSubscription::class
    ];

    public const PREDEFINED_PROVIDER_DATA_PROPERTIES = [
        ActionProvider::class    => 'actions',
        FilterProvider::class    => 'filters',
        ShortcodeProvider::class => 'shortcodes'
    ];

    private $definitions = [];

    public function __construct()
    {
        $this->definitions['subscriptionTypes']      = self::PREDEFINED_PROVIDER_SUBSCRIPTION_TYPES;
        $this->definitions['providerDataProperties'] = self::PREDEFINED_PROVIDER_DATA_PROPERTIES;
    }

    /**
     * @param int $type
     * @param string|null $key
     *
     * @return mixed
     * @throws \Exception
     */
    public function getDefinition(int $type, string $key = null)
    {
        switch ($type) {
            case self::SUBSCRIPTION:
                return $key ? $this->definitions['subscriptionTypes'][$key] : $this->definitions['subscriptionTypes'];
                break;
            case self::PROPERTY:
                return $key ? $this->definitions['providerDataProperties'][$key] : $this->definitions['providerDataProperties'];
                break;
        }

        throw new Exception("Unknown definition");
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
    public function getProviderDataProperties(): array
    {
        return $this->definitions['providerDataProperties'];
    }

    /**
     * @return array|string[]
     */
    public function getProviderMethods(): array
    {
        return $this->definitions['creationMethods'];
    }

    public function registerSubscriptionType(string $providerClass, string $dataClass, $creationMethod = null): void
    {
        /**
         *
         * Todo: implement
         *
         * Need provider class
         * Need data / subscription class
         * Need creation method in form of Factory class / Callable / null = dataclass without constructor
         */
    }
}