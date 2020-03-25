<?php

namespace Jascha030\WPSI\Manager;

use Exception;
use Jascha030\WPSI\Provider\ActionProvider;
use Jascha030\WPSI\Provider\FilterProvider;
use Jascha030\WPSI\Provider\ShortcodeProvider;
use Jascha030\WPSI\Provider\SubscriptionProvider;
use Jascha030\WPSI\Subscription\Hook\ActionSubscription;
use Jascha030\WPSI\Subscription\Hook\FilterSubscription;
use Jascha030\WPSI\Subscription\Hook\ShortcodeSubscription;

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
     * @param SubscriptionProvider $provider
     */
    public function register(SubscriptionProvider $provider)
    {
        if (! in_array($provider, $this->providers)) {
            $this->providers[] = $provider;
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
     * @param SubscriptionProvider $provider
     * @param string $type
     *
     * @return array
     */
    private function createSubscriptions(SubscriptionProvider $provider, $type)
    {
        $data              = $this->getProvidedData($provider, $type);
        $subscriptionClass = self::AVAILABLE_TYPES[$type];

        $subscriptions = [];

        if ($type === ActionProvider::class || $type === FilterProvider::class) {
            foreach ($data as $name => $parameters) {
                if (is_array($parameters) && ! is_int($parameters[1])) { // Multiple methods hooked
                    // to one tag
                    foreach ($parameters as $actionParams) {
                        $subscription = self::createSubscription($provider, $name, $actionParams, $subscriptionClass);

                        $subscriptions[$subscription->getUuid()] = $subscription;
                    }
                } else {
                    $subscription = self::createSubscription($provider, $name, $parameters, $subscriptionClass);

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
     * @param $provider
     * @param $tag
     * @param $parameters
     *
     * @param $type
     *
     * @return bool|ActionSubscription|FilterSubscription
     */
    private function createSubscription($provider, $tag, $parameters, $type)
    {
        if (is_string($parameters)) {
            $subscription = new $type($tag, [$provider, $parameters]);
        } elseif (is_array($parameters) && isset($parameters[0])) {

            $subscription = new $type($tag, [$provider, $parameters[0]], isset($parameters[1]) ? $parameters[1] : 10,
                isset($parameters[2]) ? $parameters[2] : 1);
        }

        return (isset($subscription)) ? $subscription : false;
    }

    /**
     * @param SubscriptionProvider $provider
     * @param $type
     *
     * @return array|mixed
     */
    private function getProvidedData(SubscriptionProvider $provider, $type)
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

    private function createSubscriptionsFromProviderData()
    {
        foreach ($this->providers as $provider) {
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
