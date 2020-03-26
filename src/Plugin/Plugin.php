<?php

namespace Jascha030\WPSI\Plugin;

use Closure;
use Jascha030\WPSI\Exception\DoesNotImplementProviderException;
use Jascha030\WPSI\Exception\InstanceNotAvailableException;
use Jascha030\WPSI\Manager\ItemTypes;
use Jascha030\WPSI\Manager\SubscriptionManager;
use Jascha030\WPSI\Provider\SubscriptionProvider;

/**
 * Class Plugin
 *
 * @package Jascha030\WPSI\Plugin
 */
class Plugin
{
    public static $subscriptionManager = null;

    protected $providerDependencies = [];

    /**
     * WordpressPlugin constructor.
     *
     * @param array $providers
     *
     * @param bool $run
     *
     * @throws DoesNotImplementProviderException
     * @throws InstanceNotAvailableException
     */
    public function __construct($providers = [], $run = true)
    {
        $this->createSubscriptionManager();

        if (! empty($this->providerDependencies)) {
            $providers = array_merge($this->providerDependencies, $providers);
        }

        foreach ($providers as $provider) {
            self::registerProvider($provider);
        }

        if ($run) {
            $this->run();
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
