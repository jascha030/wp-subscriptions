<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriptionException;
use Jascha030\WPSI\Subscription\Subscription;

/**
 * Interface Subscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
abstract class Subscriber
{
    /**
     * @return void|bool
     * @throws DoesNotImplementSubscriptionException
     */
    final public function register() {
        $subscriptions = $this->createSubscriptions();

        if (!is_array($subscriptions) || is_array($subscriptions) && empty($subscriptions)) {
            return false;
        }

        foreach ($this->createSubscriptions() as $subscription) {
            if ($subscription instanceof Subscription) {
                $subscription->subscribe();
            } else {
                throw new DoesNotImplementSubscriptionException();
            }
        }
    }

    abstract public function getSubscriptions();

    abstract protected function createSubscriptions();

    /**
     * @param $key
     * @param $method
     *
     * @return bool
     */
    abstract public function setSubscription($key, $method);
}
