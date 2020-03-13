<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriberException;

/**
 * Class DependencySubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
class DependencySubscriber
{
    /**
     * @var array
     */
    public static $dependencies = [];

    /**
     * DependencySubscriber constructor.
     *
     * @param array $dependencies
     */
    public function __construct($dependencies = [])
    {
        self::$dependencies = array_merge($this->getDependencies(), $dependencies);
        $this->initDependencies();
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        $class = get_called_class();
        return $class::$dependencies;
    }

    /**
     * @throws DoesNotImplementSubscriberException
     */
    public function initDependencies()
    {
        foreach ($this->getDependencies() as $dependency) {
            $i = class_implements($dependency);
            if (in_array(Subscriber::class, $i)) {
                /** @var SubscriberTrait $class */
                $class = new $dependency();
                $class->run();
            }
        }
    }
}
