<?php

namespace Jascha030\WPSI\Service\Container;

use Exception;

/**
 * Class ServiceContainer
 *
 * @package Jascha030\WPSI\Service\Container
 */
class ServiceContainer
{
    private $services = [];

    /**
     * ServiceContainer constructor.
     *
     * @param array $services
     */
    public function __construct($services = [])
    {
        foreach ($services as $service) {
            if (class_exists($service)) {
                $this[$service] = function () use ($service) {
                    static $_service;

                    if (null !== $_service) {
                        return $_service;
                    }

                    $_service = (is_string($service)) ? new $service() : $service;

                    return $_service;
                };
            }
        }
    }

    /**
     * @param $key
     *
     * @return bool|mixed
     * @throws Exception
     */
    public function get($key)
    {
        if ($this->has($key)) {
            throw new Exception("Service does not exist or is not loaded in plugin");
        }

        return call_user_func($this->services[$key]);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return (array_key_exists($key, $this->services));
    }
}
