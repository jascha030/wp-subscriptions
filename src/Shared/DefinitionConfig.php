<?php

namespace Jascha030\WP\Subscriptions\Shared;

use Jascha030\WP\Subscriptions\ActionSubscription;
use Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException;
use Jascha030\WP\Subscriptions\Exception\DoesNotImplementSubscriptionException;
use Jascha030\WP\Subscriptions\Exception\InvalidArgumentException;
use Jascha030\WP\Subscriptions\Exception\SubscriptionException;
use Jascha030\WP\Subscriptions\FilterSubscription;
use Jascha030\WP\Subscriptions\Provider\ActionProvider;
use Jascha030\WP\Subscriptions\Provider\FilterProvider;
use Jascha030\WP\Subscriptions\Provider\ShortcodeProvider;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;
use Jascha030\WP\Subscriptions\ShortcodeSubscription;

class DefinitionConfig extends Singleton
{
    public const SUBSCRIPTION = 0;
    public const PROPERTY = 1;

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

    private $definitions;

    public function __construct()
    {
        $this->definitions = [
            'subscriptionTypes'      => self::PREDEFINED_PROVIDER_SUBSCRIPTION_TYPES,
            'providerDataProperties' => self::PREDEFINED_PROVIDER_DATA_PROPERTIES
        ];
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
        if ($type === self::SUBSCRIPTION) {
            return $key ? $this->definitions['subscriptionTypes'][$key] : $this->definitions['subscriptionTypes'];
        }
        if ($type === self::PROPERTY) {
            return $key ? $this->definitions['providerDataProperties'][$key] : $this->definitions['providerDataProperties'];
        }

        throw new SubscriptionException("Unknown definition for: {$type} - {$key}");
    }

    /**
     * Register new Subscription type
     *
     * @param string $providerClass
     * @param string $subscriptionClass
     * @param string $property
     *
     * @throws \Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException
     * @throws \Jascha030\WP\Subscriptions\Exception\DoesNotImplementSubscriptionException
     * @throws \Jascha030\WP\Subscriptions\Exception\InvalidArgumentException
     */
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
