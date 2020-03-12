<?php

namespace Jascha030\WPSI\Subscriber;

trait PluginSubscriber
{
    final public function run()
    {
        return ($this->checkSubscriptionValidity($this)) ? true : false;
    }

    protected function getActions()
    {
        $class = get_called_class();

        return ($this->checkSubscriptionValidity($class)) ? $class::$actions : false;
    }

    protected function getFilters()
    {
        $class = get_called_class();

        return ($this->checkSubscriptionValidity($class)) ? $class::$filters : false;
    }

    private function checkSubscriptionValidity($class = false)
    {
        if (! $class) {
            $class = get_called_class();
        }

        $implements = class_implements($class);

        return count(array_intersect($implements, [ActionSubscriber::class, FilterSubscriber::class])) > 0;
    }
}
