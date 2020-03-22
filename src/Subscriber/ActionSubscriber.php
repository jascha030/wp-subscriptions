<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Subscription\ActionHookableSubscription;

/**
 * class ActionSubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
class ActionSubscriber extends Subscriber
{
    protected $actions = [];

    /**
     * @return array
     */
    public function getSubscriptions()
    {
        return $this->actions;
    }

    /**
     * @param $key
     * @param $method
     *
     * @return void
     */
    public function setSubscription($key, $method)
    {
        $this->actions[$key] = $method;
    }

    /**
     * @return array
     */
    protected function createSubscriptions() {
        $subscriptions = [];

        foreach ($this->getSubscriptions() as $hook => $parameters) {
            if (is_array($parameters) && is_array($parameters[0])) {
                foreach ($parameters[0] as $actionParams) {
                    $actions[] = new ActionHookableSubscription($hook, $this, $actionParams);
                }
            } else {
                $actions[] = new ActionHookableSubscription($hook, $this, $parameters);
            }
        }

        return $subscriptions;
    }
}
