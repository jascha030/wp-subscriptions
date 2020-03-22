<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriberException;
use Jascha030\WPSI\Exception\DoesNotImplementSubscriptionException;
use Jascha030\WPSI\Exception\InvalidClassException;
use Jascha030\WPSI\Subscription\Subscribable;

/**
 * Interface Subscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
abstract class Subscriber
{
    private $data = [];

    private $subscriptions = [];

    /**
     * Subscriber constructor.
     *
     * @param array $subscriptions
     * @param array $data
     */
    public function __construct(array $subscriptions = [], array $data = [])
    {
        if (! empty($subscriptions)) {
            foreach ($subscriptions as $subscription) {
                $this->setSubscription($subscription);
            }
        }

        if ($data) {
            $this->data = (! empty($this->data)) ? array_merge($this->data, $data) : $data;
        }
    }

    /**
     * @throws DoesNotImplementSubscriptionException
     */
    final public function register()
    {
        if (!empty($this->data)) {
            $this->createSubscriptionsFromData();
        }

        if (empty($this->subscriptions)) {
            throw new \Exception("No data to subscribe to.");
        }

        foreach ($this->subscriptions as $subscription) {
            if (! $this->validateSubscriptionClass($subscription)) {
                throw new DoesNotImplementSubscriptionException();
            }

            $subscription->subscribe();
        }
    }

    private function createSubscriptionsFromData()
    {
        $className = get_class($this);

        if (empty($this->subscriptionClass)) {
            throw new \Exception("\"{$className}\" has no subscription class.");
        }

        if (! $this->validateSubscriptionClass($this->subscriptionClass)) {
            throw new DoesNotImplementSubscriptionException("\"{$this->subscriptionClass}\" does not implement Subscription.");
        }

        $subscriptionClass = $this->subscriptionClass;
        $subscriptionData  = $this->data;

        if (! is_array($subscriptionData)) {
            throw new \Exception("Invalid data.");
        }

        foreach ($subscriptionData as $data) {
            if (is_array($data)) {
                /** @var Subscribable $subscription */
                $subscription = new $subscriptionClass($data);
                $this->setSubscription($subscription);
            }
        }
    }

    /**
     * @param Subscribable $subscription
     */
    public function setSubscription(Subscribable $subscription)
    {
        $this->subscriptions[] = $subscription;
    }

    /**
     * @param $class
     *
     * @return bool
     */
    private function validateSubscriptionClass($class)
    {
        if (! is_string($class) && ! is_object($class)) {
            return false;
        }

        $implements = class_implements($class);

        return (class_exists($class)) ? in_array(Subscribable::class, $implements) : false;
    }
}
