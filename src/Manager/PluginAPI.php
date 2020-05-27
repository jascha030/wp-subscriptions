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
class PluginAPI extends SubscriptionManager
{
    protected static $instance;

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

        if ($create) {
            $this->create();
        }

        $this::$instance = $this;
    }

    public static function listProviders()
    {
        return (static::getInstance())->getList(ItemTypes::PROVIDERS);
    }

    /**
     * @return mixed
     */
    public static function listSubscriptions()
    {
        return (static::getInstance())->getList(ItemTypes::SUBSCRIPTIONS);
    }

    /**
     * @return mixed
     */
    public static function listFailedSubscriptions()
    {
        return (static::getInstance())->getList(ItemTypes::FAILED_SUBSCRIPTIONS);
    }

    protected function create()
    {
        array_walk($this->providers, array($this, 'register'));
        $this->run();
    }
}
