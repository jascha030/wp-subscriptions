<?php

namespace Jascha030\WPSI\Provider;

use Exception;
use Jascha030\WPSI\Manager\SubscriptionManager;
use Jascha030\WPSI\Plugin\Plugin;

trait Provider
{
    public function getActions()
    {
        return (in_array(ActionProvider::class, class_implements($this)) && property_exists($this,
                'actions')) ? $this->actions : false;
    }

    public function getFilters()
    {
        return (in_array(FilterProvider::class, class_implements($this)) && property_exists($this,
                'filters')) ? $this->filters : false;
    }

    public function getShortcodes()
    {
        return (in_array(ShortcodeProvider::class, class_implements($this)) && property_exists($this,
                'shortcodes')) ? $this->shortcodes : false;
    }

    /**
     * @param SubscriptionManager|null $subscriptionManager
     *
     * @throws Exception
     */
    final public function register(SubscriptionManager $subscriptionManager = null)
    {
        if ($subscriptionManager) {
            $subscriptionManager->register($this);
        } else {
            Plugin::registerProvider($this);
        }
    }
}
