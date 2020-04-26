<?php

namespace Jascha030\WP\Subscriptions\Manager;

use Closure;
use Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException;
use Jascha030\WP\Subscriptions\Exception\InstanceNotAvailableException;
use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;

/**
 * Class Plugin
 *
 * @package Jascha030\WP\Subscriptions\Plugin
 */
class PluginAPI
{
    public static $subscriptionManager = null;

    protected $providers = [];

    /**
     * WordpressPlugin constructor.
     *
     * @param array $providers
     *
     * @param bool $create
     *
     * @throws DoesNotImplementProviderException
     * @throws InstanceNotAvailableException
     */
    public function __construct($providers = [], $create = true)
    {
        $this->providers = $providers;

        $this->createSubscriptionManager();

        if ($create) {
            $this->create();
        }
    }

    /**
     * @return mixed
     * @throws InstanceNotAvailableException
     */
    public static function getSubscriptionManager()
    {
        if (self::$subscriptionManager instanceof Closure) {
            return call_user_func(self::$subscriptionManager);
        } else {
            throw new InstanceNotAvailableException("No SubscriptionManager instance available");
        }
    }

    /**
     * @param SubscriptionProvider $provider
     *
     * @throws DoesNotImplementProviderException
     * @throws InstanceNotAvailableException
     */
    public static function registerProvider($provider)
    {
        /** @var SubscriptionManager $manager */
        $manager = self::getSubscriptionManager();
        $manager->register($provider);
    }

    /**
     * @return mixed
     * @throws InstanceNotAvailableException
     */
    public static function listProviders()
    {
        $manager = self::getSubscriptionManager();

        return $manager->getList(ItemTypes::PROVIDERS);
    }

    /**
     * @return mixed
     * @throws InstanceNotAvailableException
     */
    public static function listSubscriptions()
    {
        $manager = self::getSubscriptionManager();

        return $manager->getList(ItemTypes::SUBSCRIPTIONS);
    }

    /**
     * @return mixed
     * @throws InstanceNotAvailableException
     */
    public static function listFailedSubscriptions()
    {
        $manager = self::getSubscriptionManager();

        return $manager->getList(ItemTypes::FAILED_SUBSCRIPTIONS);
    }

    /**
     * @param bool $run
     *
     * @throws DoesNotImplementProviderException
     * @throws InstanceNotAvailableException
     */
    protected function create($run = true)
    {
        foreach ($this->providers as $provider) {
            self::registerProvider($provider);
        }

        if ($run) {
            $this->run();
        }
    }

    /**
     * @throws InstanceNotAvailableException
     */
    protected function run()
    {
        $manager = self::getSubscriptionManager();
        $manager->run();
    }

    private function createSubscriptionManager()
    {
        if (! $this::$subscriptionManager instanceof Closure) {
            $this::$subscriptionManager = function () {
                static $_instance;

                if ($_instance !== null) {
                    return $_instance;
                }

                $_instance = new SubscriptionManager();

                return $_instance;
            };
        }
    }
}
