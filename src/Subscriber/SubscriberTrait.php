<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriberException;
use Jascha030\WPSI\Subscription\SubscriptionManager;

/**
 * Trait Subscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
trait SubscriberTrait
{
    public function run()
    {
        if ($this->checkSubscriptionValidity($this)) {
            SubscriptionManager::register($this);
        } else {
            throw new DoesNotImplementSubscriberException();
        }
    }

    /**
     * @return mixed
     */
    public function getActions()
    {
        $class = get_called_class();

        return $class::$actions;
    }

    public function addAction($hook, $method)
    {
        $newAction = [$hook => $method];

        $class = get_called_class();
        if ($class instanceof ActionSubscriber) {
            $class::$actions = (empty($class::$actions)) ? $newAction: array_merge($class::$actions, $newAction);
        }
    }

    /**
     * @return mixed
     */
    public function getFilters()
    {
        $class = get_called_class();

        return $class::$filters;
    }

    /**
     * @return mixed
     */
    public function getShortcodes()
    {
        $class = get_called_class();

        return $class::$shortcodes;
    }

    /**
     * @param bool $class
     *
     * @return bool
     */
    private function checkSubscriptionValidity($class = false)
    {
        if (! $class) {
            $class = get_called_class();
        }

        $implements = class_implements($class);

        return in_array(Subscriber::class, $implements);
    }
}
