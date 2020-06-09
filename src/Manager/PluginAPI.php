<?php

namespace Jascha030\WP\Subscriptions\Manager;

use Jascha030\WP\Subscriptions\Shared\Container\WordpressSubscriptionContainer;
use Jascha030\WP\Subscriptions\Shared\Singleton;

class PluginAPI extends Singleton
{
    public function __construct($providers = [], $create = true)
    {
        $container = WordpressSubscriptionContainer::getInstance();

        foreach ($providers as $provider) {
            $abstract = $this->getAbstract($provider);
            if ($abstract) {
                $container->register($abstract, $provider);
            }
        }

        if ($create) {
            $container->run();
        }
    }

    public static function listProviders()
    {
        return (WordpressSubscriptionContainer::getInstance())->getList(ItemTypes::PROVIDERS);
    }

    public static function listSubscriptions()
    {
        return (WordpressSubscriptionContainer::getInstance())->getList(ItemTypes::SUBSCRIPTIONS);
    }

    public static function listFailedSubscriptions()
    {
        return (WordpressSubscriptionContainer::getInstance())->getList(ItemTypes::FAILED_SUBSCRIPTIONS);
    }

    protected function getAbstract($provider)
    {
        if (is_string($provider)) {
            $abstract = $provider;
        } else {
            $abstract = is_object($provider) ? get_class($provider) : null;
        }

        return $abstract;
    }
}
