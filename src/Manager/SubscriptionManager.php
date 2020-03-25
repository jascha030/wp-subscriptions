<?php

namespace Jascha030\WPSI\Manager;

use Closure;
use Exception;
use Jascha030\WPSI\Exception\DoesNotImplementProviderException;
use Jascha030\WPSI\Provider\ActionProvider;
use Jascha030\WPSI\Provider\FilterProvider;
use Jascha030\WPSI\Provider\ShortcodeProvider;
use Jascha030\WPSI\Provider\StaticProvider\StaticActionProvider;
use Jascha030\WPSI\Provider\StaticProvider\StaticFilterProvider;
use Jascha030\WPSI\Provider\SubscriptionProvider;
use Jascha030\WPSI\Subscription\Hook\ActionSubscription;
use Jascha030\WPSI\Subscription\Hook\FilterSubscription;
use Jascha030\WPSI\Subscription\Hook\ShortcodeSubscription;
use ReflectionException;
use ReflectionMethod;

/**
 * Class SubscriptionManager
 */
class SubscriptionManager
{
    const AVAILABLE_TYPES = [
        ActionProvider::class       => ActionSubscription::class,
        StaticActionProvider::class => ActionSubscription::class,
        FilterProvider::class       => FilterSubscription::class,
        StaticFilterProvider::class => FilterSubscription::class,
        ShortcodeProvider::class    => ShortcodeSubscription::class
    ];

    private $providers = [];

    private $subscriptions = [];

    private $failedSubscriptions = [];

    public function run()
    {
        $this->createSubscriptionsFromProviderData();

        foreach ($this->subscriptions as &$subscription) {
            try {
                $subscription->subscribe();
            } catch (Exception $exception) {
                $this->failedSubscriptions[$subscription->getUuid()] = $exception->getMessage();
            }
        }
    }

    /**
     * @param SubscriptionProvider|string $provider
     * @param bool $lazyLoad
     *
     * @throws DoesNotImplementProviderException
     */
    public function register($provider, $lazyLoad = true)
    {
        if (is_object($provider) && $provider instanceof SubscriptionProvider) {
            $this->registerProvider($provider);
        } elseif (is_string($provider) && in_array(SubscriptionProvider::class, class_implements($provider))) {
            if (! $lazyLoad) {
                $this->registerProvider(new $provider());
            }
            $this->registerStaticProvider($provider);
        } else {
            throw new DoesNotImplementProviderException();
        }
    }

    /**
     * @param int $type
     *
     * @return array
     */
    public function getList($type = ItemTypes::PROVIDERS)
    {
        $data = [];
        $list = [];

        switch ($type) {
            case ItemTypes::PROVIDERS:
                $data = $this->providers;
                break;

            case ItemTypes::SUBSCRIPTIONS:
                $data = $this->subscriptions;
                break;

            case ItemTypes::FAILED_SUBSCRIPTIONS:
                $data = $this->failedSubscriptions;
                break;
        }

        foreach ($data as $key => $item) {
            if (is_object($item)) {
                $item = get_class($item);
            }
            $list[$key] = (is_object($item)) ? get_class($item) : $item;
        }

        return $list;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return void|Closure
     */
    public function __call($name, $arguments)
    {
        // Overkill super lazy loader 3000
        if (substr($name, 0, 14) === "providerMethod") {
            $args = explode('__', substr($name, 13));

            return function () use ($args) {
                $provider = call_user_func($this->providers[$args[0]]);

                return call_user_func([$provider, $args[1]]);
            };
        }
    }

    /**
     * @param SubscriptionProvider $provider
     */
    private function registerProvider(SubscriptionProvider $provider)
    {
        if (! in_array($provider, $this->providers)) {
            $this->providers[get_class($provider)] = $provider;
        }
    }

    /**
     * @param string $provider
     */
    private function registerStaticProvider(string $provider)
    {
        if (! array_key_exists($provider, $this->providers)) {
            $this->providers[$provider] = function () use ($provider) {
                static $_provider;

                if ($_provider !== null) {
                    return $_provider;
                }

                $_provider = new $provider();

                return $_provider;
            };
        }
    }

    /**
     * @param SubscriptionProvider|string $provider
     * @param string $type
     *
     * @return array
     * @throws ReflectionException
     */
    private function createSubscriptions($provider, $type)
    {
        $data              = $this->getProvidedData($provider, $type);
        $subscriptionClass = self::AVAILABLE_TYPES[$type];

        $subscriptions = [];

        if ($subscriptionClass === ActionSubscription::class || $subscriptionClass === FilterSubscription::class) {
            foreach ($data as $name => $parameters) {
                if (is_array($parameters) && ! is_int($parameters[1])) { // Multiple methods hooked
                    // to one tag
                    foreach ($parameters as $actionParams) {
                        $subscription = self::createHookSubscription($provider, $name, $actionParams,
                            $subscriptionClass);

                        $subscriptions[$subscription->getUuid()] = $subscription;
                    }
                } else {
                    $subscription = self::createHookSubscription($provider, $name, $parameters, $subscriptionClass);

                    $subscriptions[$subscription->getUuid()] = $subscription;
                }
            }
        } elseif ($type === ShortcodeProvider::class) {
            foreach ($data as $shortCodeData) {
                /** @var ShortcodeSubscription $subscription */
                $subscription = new $subscriptionClass($shortCodeData[0], $shortCodeData[1]);

                $subscriptions[$subscription->getUuid()] = $subscription;
            }
        }

        return $subscriptions;
    }

    /**
     * @param SubscriptionProvider|string $provider
     * @param $tag
     * @param $parameters
     *
     * @param $type
     *
     * @return bool|ActionSubscription|FilterSubscription
     * @throws ReflectionException
     */
    private function createHookSubscription($provider, $tag, $parameters, $type)
    {
        $methodToCall = (is_array($parameters)) ? $parameters[0] : $parameters;

        if (is_string($provider)) {
            $reflectionMethod = new ReflectionMethod($provider, $methodToCall);

            if (! $reflectionMethod->isStatic()) { // Okay this is overkill I know... but I was bored.
                $methodToCall = "providerMethod{$provider}__{$methodToCall}";
                $provider     = $this;
            }
        }

        $callable          = [$provider, $methodToCall];
        $priority          = (is_array($parameters) && isset($parameters[1])) ? $parameters[1] : 10;
        $acceptedArguments = (is_array($parameters) && isset($parameters[2])) ? $parameters[2] : 1;

        return new $type($tag, $callable, $priority, $acceptedArguments);
    }

    /**
     * @param SubscriptionProvider|string $provider
     * @param string $type
     *
     * @return array|mixed
     */
    private function getProvidedData($provider, $type)
    {
        $data = [];

        switch ($type) {
            case ActionProvider::class:
                $data = $provider->getActions();
                break;

            case FilterProvider::class:
                $data = $provider->getFilters();
                break;

            case ShortcodeProvider::class:
                $data = $provider->getShortcodes();
                break;

            case StaticActionProvider::class:
                $data = $provider::getStaticActions();
                break;

            case StaticFilterProvider::class:
                $data = $provider::getStaticFilters();
                break;
        }

        return $data;
    }

    private function createSubscriptionsFromProviderData()
    {
        foreach ($this->providers as $className => $provider) {
            if ($provider instanceof Closure) {
                $provider = $className;
            }

            $implements = class_implements($provider);

            foreach (self::AVAILABLE_TYPES as $type => $subscriberName) {
                if (in_array($type, $implements)) {
                    $newSubscriptions    = $this->createSubscriptions($provider, $type);
                    $this->subscriptions = array_merge($this->subscriptions, $newSubscriptions);
                }
            }
        }
    }
}
