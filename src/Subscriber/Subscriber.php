<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriberException;
use Jascha030\WPSI\Subscription\SubscriptionManager;

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

    public function getActions()
    {
        $class = get_called_class();

        return $class::$actions;
    }

    public function getFilters()
    {
        $class = get_called_class();

        return $class::$filters;
    }

    public function getShortcodes()
    {
        $class = get_called_class();

        return $class::$shortcodes;
    }

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
