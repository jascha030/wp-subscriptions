<?php

namespace Jascha030\WPOL\Subscription\Manager;

use Exception;
use Jascha030\WPOL\Subscription\ActionSubscription;
use Jascha030\WPOL\Subscription\Exception\DoesNotImplementProviderException;
use Jascha030\WPOL\Subscription\FilterSubscription;
use Jascha030\WPOL\Subscription\Provider\ActionProvider;
use Jascha030\WPOL\Subscription\Provider\FilterProvider;
use Jascha030\WPOL\Subscription\Provider\ShortcodeProvider;
use Jascha030\WPOL\Subscription\Provider\SubscriptionProvider;
use Jascha030\WPOL\Subscription\ShortcodeSubscription;
use ReflectionException;
use ReflectionMethod;

/**
 * Class SubscriptionManager
 */
class SubscriptionManager
{
    const AVAILABLE_TYPES = [
        ActionProvider::class    => ActionSubscription::class,
        FilterProvider::class    => FilterSubscription::class,
        ShortcodeProvider::class => ShortcodeSubscription::class
    ];

    private $providers = [];

    private $subscriptions = [];

    private $failedSubscriptions = [];

    /**
     * @throws ReflectionException
     */
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
     *
     * @throws DoesNotImplementProviderException
     */
    public function register($provider)
    {
        if (! in_array($provider, $this->providers)) {
            if (in_array(SubscriptionProvider::class, class_implements($provider))) {
                $this->providers[(is_string($provider)) ? $provider : get_class($provider)] = $provider;
            } else {
                throw new DoesNotImplementProviderException();
            }
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
        $callable     = [$provider, $methodToCall];

        if (is_string($provider)) {
            $reflectionMethod = new ReflectionMethod($provider, $methodToCall);

            if (! $reflectionMethod->isStatic()) {
                $callable = function (...$params) use ($provider, $methodToCall) {
                    static $_instance;

                    if ($_instance !== null) {
                        return call_user_func([$_instance, $methodToCall], ...$params);
                    }

                    $_instance = new $provider();

                    return $_instance;
                };
            }
        }

        $priority          = (is_array($parameters)) ? $parameters[1] ?? 10 : 10;
        $acceptedArguments = (is_array($parameters)) ? $parameters[2] ?? 1 : 1;

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
        }

        return $data;
    }

    /**
     * @throws ReflectionException
     */
    private function createSubscriptionsFromProviderData()
    {
        foreach ($this->providers as $className => $provider) {
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
