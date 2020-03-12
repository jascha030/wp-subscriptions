<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriberException;
use Jascha030\WPSI\Subscription\SubscriptionManager;

/**
 * Trait Subscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
trait Subscriber
{
    final public function run()
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

        return count(array_intersect($implements,
                [ShortcodeSubscriber::class, ActionSubscriber::class, FilterSubscriber::class])) > 0;
    }
}
