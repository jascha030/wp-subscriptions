<?php

namespace Jascha030\WPSI\Plugin;

use Closure;
use Jascha030\WPSI\Exception\DoesNotImplementProviderException;
use Jascha030\WPSI\Exception\InstanceNotAvailableException;
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

    private $providerDependencies = [];

    /**
     * WordpressPlugin constructor.
     *
     * @param array $providers
     *
     * @throws DoesNotImplementProviderException
     * @throws InstanceNotAvailableException
     */
    public function __construct($providers = [])
    {
        $this->createSubscriptionManager();

        if (! empty($this->providerDependencies)) {
            $providers = array_merge($this->providerDependencies, $providers);
        }

        foreach ($providers as $provider) {
            if (in_array(SubscriptionProvider::class, class_implements($provider))) {
                self::registerProvider($provider);
            } else {
                throw new DoesNotImplementProviderException();
            }
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
            throw new InstanceNotAvailableException("no instance available");
        }
    }

    /**
     * @param SubscriptionProvider $provider
     *
     * @throws InstanceNotAvailableException
     */
    public static function registerProvider(SubscriptionProvider $provider)
    {
        /** @var SubscriptionManager $manager */
        $manager = self::getSubscriptionManager();
        $manager->register($provider);
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
