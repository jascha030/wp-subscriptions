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
class ProviderSubscription extends Subscription
{
    private $className;

    private $arguments = [];

    private $object = [];

    /**
     * DependencySubscription constructor.
     *
     * @param $className
     * @param null $arguments
     */
    public function __construct($className, $arguments = null)
    {
        $this->className = $className;

        $this->arguments = $arguments;
    }

    public function subscribe()
    {
        parent::subscribe();

        $className = $this->className;

        if (! class_exists($className)) {
            throw new InvalidClassException("Class with name: {$className} does not exist.");
        }

        if (! is_subclass_of($className, Subscriber::class)) {
            throw new DoesNotImplementSubscriberException("Class: '{$className}' is not a valid Subscriber.");
        }

        if ($this->arguments) {
            $this->object = new $className(...$this->arguments);
        } else {
            $this->object = new $className();
        }
    }
}
