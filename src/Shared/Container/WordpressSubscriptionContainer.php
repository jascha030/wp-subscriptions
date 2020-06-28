<?php

namespace Jascha030\WP\Subscriptions\Shared\Container;

use Exception;
use Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;
use Jascha030\WP\Subscriptions\Runnable\Runnable;
use Jascha030\WP\Subscriptions\Shared\DefinitionConfig;
use Jascha030\WP\Subscriptions\Subscription;

class WordpressSubscriptionContainer extends Container implements Runnable
{
    protected $definitions;

    protected $providerBindings = [];

    protected $subscriptions = [];

    protected $failed = [];

    public function __construct()
    {
        $this->definitions = new DefinitionConfig();
    }

    public function getSubscriptionById(string $id)
    {
        return $this->subscriptions[$id];
    }

    public function updateHookSubscription(string $id)
    {
        $this->subscriptions[$id]->called++;
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

        return (property_exists(get_class($provider), $prop)) ? $provider::$$prop : [];
    }

    public function bindProvider(string $abstract, $concrete = null): void
    {
        if ($this->canBindProvider($abstract)) {
            $this->providerBindings[] = $abstract;
        }

        $this->bind($abstract, $concrete);
    }

    /**
     * @param $abstract
     * @param null $provider
     *
     * @throws \Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException
     */
    final public function register($abstract, $provider = null): void
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
     * Init and activate all subscriptions
     *
     * @throws \Exception
     */
    final public function run(): void
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
     * @param int $type
     * @param string|null $key
     *ShortcodeSubscription
     *
     * @return mixed
     * @throws \Exception
     */
    private function getDefinition(int $type, string $key = null)
    {
        return $this->definitions->getDefinition($type, $key);
    }

    /**
     * @param $provider
     * @param string $subscriptionClass
     *
     * @return array
     * @throws \Exception
     */
    private function createSubscriptions($provider, string $subscriptionClass): array
    {
        if (! is_subclass_of($subscriptionClass, Subscription::class)) {
            return [];
        }

        $provider = is_string($provider) ? $this->resolve($provider) : $provider;

        try {
            return $subscriptionClass::create($provider, $subscriptionClass);
        } catch (Exception $e) {
            return [];
        }
    }

    private function canBindProvider($abstract): bool
    {
        $abstract = (is_object($abstract)) ? get_class($abstract) : $abstract;

        return is_subclass_of($abstract, SubscriptionProvider::class) && ! $this->bound($abstract);
    }

    /**
     * @throws \Exception
     */
    private function initSubscriptions(): void
    {
        $subscriptions = [];

        foreach ($this->providerBindings as $abstract) {
            $abstract   = $this->concreteBinding($abstract);
            $definition = $this->getDefinition(DefinitionConfig::SUBSCRIPTION);

            foreach ($definition as $subscriptionType) {
                array_push($subscriptions, ...$this->createSubscriptions($abstract, $subscriptionType));
            }
        }

        foreach ($subscriptions as $subscription) {
            $this->subscriptions[$subscription->getId()] = $subscription;
        }
    }
}

/**
 * function to call as replacement for entry in $_GLOBALS
 *
 * @return \Jascha030\WP\Subscriptions\Shared\Container\WordpressSubscriptionContainer
 */
function WPSC()
{
    return WordpressSubscriptionContainer::getInstance();
}
