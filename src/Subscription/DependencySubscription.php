<?php

namespace Jascha030\WPSI\Subscription;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriberException;

/**
 * Class DependencySubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class DependencySubscription implements Subscription
{
    private $className;

    private $arguments = [];

    /**
     * DependencySubscription constructor.
     *
     * @param $className
     * @param array $arguments
     */
    public function __construct($className, $arguments = [])
    {
        $this->className = $className;

        $this->arguments = $arguments;
    }

    /**
     * @throws DoesNotImplementSubscriberException
     */
    public function subscribe()
    {
        $className = $this->className;

        if (! class_exists($className)) {
            throw new DoesNotImplementSubscriberException();
        }

        $class = new $className();

        try {
            $class->register();
        } catch (\Exception $exception) {
            throw new DoesNotImplementSubscriberException();
        }
    }
}
