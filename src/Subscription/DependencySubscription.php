<?php

namespace Jascha030\WPSI\Subscription;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriberException;
use Jascha030\WPSI\Exception\InvalidClassException;
use Jascha030\WPSI\Subscriber\Subscriber;

/**
 * Class DependencySubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class DependencySubscription implements Subscribable
{
    private $className;

    private $arguments = [];

    /**
     * DependencySubscription constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->create(...$data);
    }

    /**
     * @throws DoesNotImplementSubscriberException
     * @throws InvalidClassException
     */
    public function subscribe()
    {
        $className = $this->className;

        if (! class_exists($className)) {
            throw new InvalidClassException("Class with name: {$className} does not exist.");
        }

        if (! is_subclass_of($className, Subscriber::class)) {
            throw new DoesNotImplementSubscriberException("Class: '{$className}' is not a valid Subscriber.");
        }

        $class = new $className();
        $class->register();
    }

    /**
     * @param string $className
     * @param array $arguments
     */
    private function create(string $className, $arguments = [])
    {
        $this->className = $className;

        $this->arguments = $arguments;
    }
}
