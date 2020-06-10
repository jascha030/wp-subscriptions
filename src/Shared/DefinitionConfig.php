<?php

namespace Jascha030\WP\Subscriptions\Shared;

use http\Exception\InvalidArgumentException;
use Jascha030\WP\Subscriptions\ActionSubscription;
use Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException;
use Jascha030\WP\Subscriptions\Exception\DoesNotImplementSubscriptionException;
use Jascha030\WP\Subscriptions\Exception\SubscriptionException;
use Jascha030\WP\Subscriptions\FilterSubscription;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\FilterProvider;
use Jascha030\WP\Subscriptions\Provider\ShortcodeProvider;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;
use Jascha030\WP\Subscriptions\ShortcodeSubscription;

class DefinitionConfig
{
    public const SUBSCRIPTION = 0;
    public const PROPERTY = 1;

    /**
     * @var \Jascha030\WP\Subscriptions\Subscription[]
     */
    public const PREDEFINED_PROVIDER_SUBSCRIPTION_TYPES = [
        ActionProvider::class    => ActionSubscription::class,
        FilterProvider::class    => FilterSubscription::class,
        ShortcodeProvider::class => ShortcodeSubscription::class
    ];

    public const PREDEFINED_PROVIDER_DATA_PROPERTIES = [
        ActionSubscription::class    => 'actions',
        FilterSubscription::class    => 'filters',
        ShortcodeSubscription::class => 'shortcodes'
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

        throw new SubscriptionException("Unknown definition for: {$type} - {$key}");
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
    public function getProviderMethods(): array
    {
        return $this->definitions['creationMethods'];
    }

    public function registerSubscriptionType(string $providerClass, string $subscriptionClass, string $property): void
    {
        if (! class_exists($providerClass) || is_subclass_of($providerClass, SubscriptionProvider::class)) {
            throw new DoesNotImplementProviderException("Invalid class: {$providerClass}");
        }

        if (! class_exists($subscriptionClass) || is_subclass_of($providerClass, SubscriptionProvider::class)) {
            throw new DoesNotImplementSubscriptionException("Invalid provider class: {$subscriptionClass}");
        }

        if (! property_exists($subscriptionClass, $property)) {
            throw new InvalidArgumentException("Property: {$property}, does not exist on {$subscriptionClass}");
        }

        $this->definitions['subscriptionTypes'][$providerClass]          = $subscriptionClass;
        $this->definitions['providerDataProperties'][$subscriptionClass] = $property;
    }
}