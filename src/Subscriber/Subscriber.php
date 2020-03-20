<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriberException;
use Jascha030\WPSI\Exception\DoesNotImplementSubscriptionException;
use Jascha030\WPSI\Exception\InvalidClassException;
use Jascha030\WPSI\Subscription\Subscription;

/**
 * Interface Subscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
abstract class Subscriber
{
    private $data = [];

    private $subscriptionClass;

    private $subscriptions = [];

    /**
     * Subscriber constructor.
     *
     * @param string $class
     *
     * @param array|null $data
     *
     * @param array|null $subscriptions
     *
     * @throws DoesNotImplementSubscriberException
     * @throws InvalidClassException
     */
    public function __construct(array $data = [], string $class = null, array $subscriptions = [])
    {
        if (! empty($subscriptions)) {
            foreach ($subscriptions as $subscription) {
                $this->setSubscription($subscription);
            }
        }

        if ($data) {
            $this->data = (! empty($this->data)) ? array_merge($this->data, $data) : $data;
        }

        if ($class) {
            if (! class_exists($class)) {
                throw new InvalidClassException("Class \"{$class}\" does not exist.");
            }

            if (! $this->validateSubscriptionClass($class)) {
                throw new DoesNotImplementSubscriberException("\"{$class}\" does not implement Subscription.");
            }

            $this->subscriptionClass = $class;
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
        $subscriptionData  = $this->getData();

        if (! is_array($subscriptionData)) {
            throw new \Exception("Invalid data.");
        }

        foreach ($subscriptionData as $data) {
            if (is_array($data)) {
                /** @var Subscription $subscription */
                $subscription = new $subscriptionClass($data);
                $this->setSubscription($subscription);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Subscription $subscription
     */
    public function setSubscription(Subscription $subscription)
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

        return (class_exists($class)) ? in_array(Subscriber::class, $implements) : false;
    }
}
