<?php

namespace Jascha030\WPSI\Plugin;

use Jascha030\WPSI\Exception\DoesNotImplementProviderException;
use Jascha030\WPSI\Exception\InstanceNotAvailableException;
use Jascha030\WPSI\Service\Container\ServiceContainer;
use Jascha030\WPSI\Subscription\Manager\PluginAPI;

/**
 * Class Plugin
 *
 * @package Jascha030\WPSI\Plugin
 */
class Plugin extends PluginAPI
{
    public static $serviceContainer = null;

    /**
     * WordpressPlugin constructor.
     *
     * @param array $providers
     * @param array $services
     *
     * @throws DoesNotImplementProviderException
     * @throws InstanceNotAvailableException
     */
    public function __construct($providers = [], $services = [])
    {
        parent::__construct($providers, false);

        $this::$serviceContainer = new ServiceContainer($services);

        add_action('init', [$this, 'create']);
    }

    /**
     * @param bool $run
     *
     * @throws DoesNotImplementProviderException
     * @throws InstanceNotAvailableException
     */
    protected function create($run = true)
    {
        parent::create($run);
    }
}
