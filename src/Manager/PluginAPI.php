<?php

namespace Jascha030\WP\Subscriptions\Manager;

use Jascha030\WP\Subscriptions\Exception\DoesNotImplementProviderException;
use Jascha030\WP\Subscriptions\Exception\InstanceNotAvailableException;
use Jascha030\WP\Subscriptions\Shared\Container\WordpressSubscriptionContainer;
use Jascha030\WP\Subscriptions\Shared\Singleton;

/**
 * Class Plugin
 *
 * @package Jascha030\WP\Subscriptions\Plugin
 */
class PluginAPI extends Singleton
{
    protected $providers = [];

    public function __construct($providers = [], $create = true)
    {
        $this->providers = $providers;

        if ($create) {
            $this->create();
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

    protected function create()
    {
        $container = WordpressSubscriptionContainer::getInstance();

        foreach ($this->providers as $provider) {
            $container->register($provider);
        }
        $container->run();
    }
}
