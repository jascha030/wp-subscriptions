<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Subscription\DependencySubscription;

/**
 * Class DependencySubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
class DependencySubscriber extends Subscriber
{
    /**
     * @var array
     */
    protected $dependencies = [];

    /**
     * @return array
     */
    public function getSubscriptions()
    {
        return $this->dependencies;
    }

    /**
     * @param $key
     * @param $method
     */
    public function setSubscription($key, $method)
    {
        $this->dependencies[$key] = $method;
    }

    protected function createSubscriptions()
    {
        $dependencies = [];

        foreach ($this->getSubscriptions() as $dependency) {
            if (is_string($dependency)) {
                $className = $dependency;
                $arguments = [];
            } elseif (is_array($dependency) && count($dependency) > 1) {
                $className = $dependency[0];
                $arguments = $dependency[1];
            } else {
                continue;
            }

            if (is_subclass_of($className, Subscriber::class)) {
                $dependencies[] = new DependencySubscription($className, $arguments);
            }
        }
    }
}
