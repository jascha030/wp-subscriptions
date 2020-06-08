<?php

namespace Jascha030\WP\Subscriptions\Shared\Container;

use Exception;
use Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException;
use Jascha030\WP\Subscriptions\Manager\SubscriptionManager;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;
use Jascha030\WP\Subscriptions\Shared\DefinitionConfig;
use Jascha030\WP\Subscriptions\Subscription;

use function class_alias;

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

    /**
     * @param int $type
     * @param string|null $key
     *
     * @return mixed
     * @throws \Exception
     */
    public function getDefinition(int $type, string $key = null)
    {
        return $this->definitions->getDefinition($type, $key);
    }

    public function bindProvider(string $abstract, $concrete = null, $shared = false): void
    {
        if ($this->canBindProvider($abstract)) {
            $this->providerBindings[] = $abstract;
        }

        $this->bind($abstract, $concrete, $shared = false);
    }

    /**
     * Init and activate all subscriptions
     *
     * @throws \Exception
     */
    public function run(): void
    {
        $this->initSubscriptions();

        foreach ($this->subscriptions as $subscription) {
            try {
                $subscription->subscribe();
            } catch (Exception $exception) {
                $this->failed[$subscription->getUuid()] = $exception->getMessage();
            }
        }
    }

    /**
     * @param $abstract
     * @param null $provider
     *
     * @throws \Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException
     */
    public function register($abstract, $provider = null): void
    {
        if (is_object($abstract) && ! $provider) {
            $provider = $abstract;
            $abstract = get_class($provider);
        }

        if (! is_subclass_of($abstract, SubscriptionProvider::class)) {
            throw new DoesNotImplementProviderException($abstract);
        }

        if (! $this->bound($abstract)) {
            $this->bindProvider($abstract, $provider);
        }
    }

    /**
     * @param \Jascha030\WP\Subscriptions\Provider\SubscriptionProvider $provider
     * @param $type
     *
     * @return array
     * @throws \Exception
     */
    public function getProviderData(SubscriptionProvider $provider, $type): array
    {
        $prop = $this->getDefinition(DefinitionConfig::PROPERTY, $type);

        if (property_exists(get_class($provider), $prop)) {
            return $provider::$$prop;
        }

        return [];
    }

    /**
     * @param $provider
     * @param string $subscriptionClass
     *
     * @return array
     * @throws \Exception
     */
    protected function createSubscriptions($provider, string $subscriptionClass): array
    {
        $provider = is_string($provider) ? $this->resolve($provider) : $provider;

        if (! is_subclass_of($subscriptionClass, Subscription::class)) {
            return [];
        }

        try {
            return $subscriptionClass::create($provider, $subscriptionClass);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @throws \Exception
     */
    protected function initSubscriptions(): void
    {
        foreach ($this->providerBindings as $abstract) {
            $abstract = $this->concreteBinding($abstract);

            $this->subscriptions = [];

            foreach ($this->getDefinition(DefinitionConfig::SUBSCRIPTION) as $providerType => $subscriptionType) {
                array_push($this->subscriptions, ...$this->createSubscriptions($abstract, $subscriptionType));
            }
        }
    }

    /**
     * Check concrete for binding and return it if it exists.
     * Mainly for Backwards compatibility
     *
     * @param string $abstract
     *
     * @return mixed|string
     */
    protected function concreteBinding(string $abstract)
    {
        $binding = $this->bindings[$abstract];

        return is_object($binding['concrete']) ? $binding['concrete'] : $abstract;
    }

    protected function canBindProvider($abstract): bool
    {
        $abstract = is_object($abstract) ? get_class($abstract) : null;

        return is_subclass_of($abstract, SubscriptionProvider::class) && ! $this->bound($abstract);
    }
}

/**
 * function to call as replacement for entry in $_GLOBALS
 *
 * @return mixed
 */
function WPSC()
{
    return WordpressSubscriptionContainer::getInstance();
}

// Backwards compatibility
class_alias(WordpressSubscriptionContainer::class, SubscriptionManager::class);
