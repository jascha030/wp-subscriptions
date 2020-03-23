<?php

namespace Jascha030\WPSI\Plugin;

use Closure;
use Exception;
use Jascha030\WPSI\Manager\SubscriptionManager;
use Jascha030\WPSI\Provider\SubscriptionProvider;

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
     * @throws Exception
     */
    public static function getSubscriptionManager()
    {
        if (self::$subscriptionManager instanceof Closure) {
            return call_user_func(self::$subscriptionManager);
        } else {
            throw new Exception("no instance available");
        }
    }

    /**
     * @param SubscriptionProvider $provider
     *
     * @throws Exception
     */
    public static function registerProvider(SubscriptionProvider $provider)
    {
        /** @var SubscriptionManager $manager */
        $manager = self::getSubscriptionManager();
        $manager->register($provider);
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

    /**
     * @throws Exception
     */
    protected function run() {
        $manager = self::getSubscriptionManager();
        $manager->run();
    }
}
