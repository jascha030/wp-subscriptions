<?php

namespace Jascha030\WPSI\Subscription\Provider;

use Jascha030\WPSI\Subscription\Subscription;

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

        if ($this->arguments) {
            $this->object = new $className(...$this->arguments);
        } else {
            $this->object = new $className();
        }
    }
}
