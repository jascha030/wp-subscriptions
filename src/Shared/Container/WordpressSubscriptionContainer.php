<?php

namespace Jascha030\WP\Subscriptions\Shared\Container;

use Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;
use Jascha030\WP\Subscriptions\Shared\DefinitionConfig;

class WordpressSubscriptionContainer extends Container
{
    /**
     * @var array|\Jascha030\WP\Subscriptions\Shared\DefinitionConfig
     */
    protected $definitions = [];

    protected $providerBindings = [];

    protected $subscriptions = [];

    protected $failed = [];

    public function __construct()
    {
        $this->definitions = new DefinitionConfig();
    }

    public function getDefinition(int $type, string $key = null)
    {
        return $this->definitions->getDefinition($type, $key);
    }

    public function bind(string $abstract, $concrete = null, $shared = false)
    {
        if (is_subclass_of($abstract, SubscriptionProvider::class) && ! in_array($abstract, $this->providerBindings)) {
            $this->providerBindings[] = $abstract;
        }

        parent::bind($abstract, $concrete, $shared = false);
    }

    public function run()
    {
        $this->initSubscriptions();

        foreach ($this->subscriptions as $subscription) {
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

    public function getProviderData(SubscriptionProvider $provider, $type)
    {
        $prop = $this->getDefinition(DefinitionConfig::PROPERTY, $type);

        if (property_exists(get_class($provider), $prop)) {
            return $provider::$$prop;
        }

        return [];
    }

    /**
     * @param $provider
     * @param string $subscriptionType
     *
     * @return array
     * @throws \Exception
     */
    protected function createSubscriptions($provider, string $subscriptionType)
    {
        if (is_string($provider)) {
            $provider = $this->resolve($provider);
        }

        try {
            return call_user_func([$subscriptionType, 'create'], $provider, $subscriptionType);
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function initSubscriptions()
    {
        foreach ($this->providerBindings as $abstract) {
            $binding = $this->bindings[$abstract];

            // Backwards compatibility
            $abstract = is_object($binding['concrete']) ? $binding['concrete'] : $abstract;

            foreach ($this->getDefinition(DefinitionConfig::SUBSCRIPTION) as $providerType => $subscriptionType) {
                $this->subscriptions = array_merge(
                    $this->subscriptions,
                    $this->createSubscriptions($abstract, $subscriptionType)
                );
            }
        }
    }
}

function WPSC()
{
    return WordpressSubscriptionContainer::getInstance();
}

\class_alias(WordpressSubscriptionContainer::class, \Jascha030\WP\Subscriptions\Manager\SubscriptionManager::class);
