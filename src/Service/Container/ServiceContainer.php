<?php

namespace Jascha030\WPSI\Service\Container;

use Closure;
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
        $this->services = $services;

        $this->generateServiceClosures();
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
            throw new Exception("Service not loaded");
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

    protected function generateServiceClosures()
    {
        foreach ($this->services as $property => $service) {
            if ($service instanceof Closure) {
                continue;
            }

            if (class_exists($service)) {
                $this->services[$property] = function () use ($service) {
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
}
