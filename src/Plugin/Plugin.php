<?php

namespace Jascha030\WPSI\Plugin;

use Closure;
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

    /**
     * WordpressPlugin constructor.
     */
    public function __construct()
    {
        $this->createSubscriptionManager();
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
