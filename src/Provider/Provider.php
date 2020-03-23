<?php

namespace Jascha030\WPSI\Provider;

use Jascha030\WPSI\Manager\SubscriptionManager;
use Jascha030\WPSI\Plugin\WordpressPlugin;

trait Provider
{
    public function getActions()
    {
        return (in_array(ActionProvider::class, class_implements($this)) && property_exists($this,
                'actions')) ? $this->actions : false;
    }

    public function getFilters()
    {
        return (in_array(FilterProvider::class, class_implements($this)) && property_exists($this, 'filters')) ?
            $this->filters: false;
    }

    public function getShortcodes()
    {
        return (in_array(ShortcodeProvider::class, class_implements($this)) && property_exists($this, 'shortcodes')) ?
            $this->shortcodes: false;
    }

    final public function register(SubscriptionManager $subscriptionManager = null)
    {
        if (in_array(SubscriptionProvider::class, class_implements($this))) {
            if ($subscriptionManager) {
                $subscriptionManager->register($this);
            } else {
                try {
                    WordpressPlugin::registerProvider($this);
                } catch (\Exception $e) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
