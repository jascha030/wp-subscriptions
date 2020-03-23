<?php

namespace Jascha030\WPSI\Manager;

use Closure;
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

    /**
     * SubscriptionManager constructor.
     */
    public function __construct()
    {
//        var_dump("created");
    }

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
                if (is_array($parameters) && is_array($parameters[0])) {
                    foreach ($parameters[0] as $actionParams) {
                        $subscription                            = self::createSubscription($provider, $name,
                            $actionParams, $subscriptionClass);
                        $subscriptions[$subscription->getUuid()] = $subscription;
                    }
                } else {
                    $subscription                            = self::createSubscription($provider, $name, $parameters,
                        $subscriptionClass);
                    $subscriptions[$subscription->getUuid()] = $subscription;
                }
            }
        } elseif ($type === ShortcodeProvider::class) {
            foreach ($data as $shortcodeData) {
                /** @var ShortcodeSubscription $subscription */
                $subscription                            = new $subscriptionClass($shortcodeData[0], $shortcodeData[1]);
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
