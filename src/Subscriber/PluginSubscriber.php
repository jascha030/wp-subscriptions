<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Subscription\SubscriptionManager;

trait PluginSubscriber
{
    final public function run()
    {
        if ($this->checkSubscriptionValidity($this)) {
            SubscriptionManager::register($this);
        }
    }

    public function getActions()
    {
        $class = get_called_class();

        return ($this->checkSubscriptionValidity($class)) ? $class::$actions : false;
    }

    public function getFilters()
    {
        $class = get_called_class();

        return ($this->checkSubscriptionValidity($class)) ? $class::$filters : false;
    }

    final private function checkSubscriptionValidity($class = false)
    {
        if (! $class) {
            $class = get_called_class();
        }

        $implements = class_implements($class);

        return count(array_intersect($implements, [ActionSubscriber::class, FilterSubscriber::class])) > 0;
    }
}
