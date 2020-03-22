<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Subscription\FilterSubscription;

/**
 * class FilterSubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
class FilterSubscriber extends Subscriber
{
    protected $filters = [];

    /**
     * @return array
     */
    public function getSubscriptions()
    {
        return $this->filters;
    }

    /**
     * @param $key
     * @param $method
     *
     * @return void
     */
    public function setSubscription($key, $method)
    {
        $this->filters[$key] = $method;
    }

    /**
     * @return array
     */
    protected function createSubscriptions()
    {
        $filters = [];

        foreach ($this->getSubscriptions() as $hook => $parameters) {
            if (is_array($parameters) && is_array($parameters[0])) {
                foreach ($parameters[0] as $filterParams) {
                    $filters[] = new FilterSubscription($hook, $this, $filterParams);
                }
            } else {
                $filters[] = new FilterSubscription($hook, $this, $parameters);
            }
        }

        return $filters;
    }
}
