<?php

namespace Jascha030\WPSI\Subscription;

use Jascha030\WPSI\Subscriber\ActionSubscriber;
use Jascha030\WPSI\Subscriber\FilterSubscriber;
use Jascha030\WPSI\Subscriber\PluginSubscriber;

class SubscriptionManager
{
    public static function register(PluginSubscriber $object)
    {
        $filters       = ($object instanceof ActionSubscriber) ? self::getActionSubscriptions($object) : [];
        $actions       = ($object instanceof FilterSubscriber) ? self::getFilterSubscriptions($object) : [];
        $subscriptions = array_merge($actions, $filters);

        self::subscribeToAll($subscriptions);
    }

    private static function getActionSubscriptions(ActionSubscriber $subscriber)
    {
        $actions = [];

        if ($subscriber->getActions()) {
            foreach ($subscriber->getActions() as $hook => $parameters) {
                if (is_array($parameters) && is_array($parameters[0])) {
                    foreach ($parameters[0] as $actionParams) {
                        $actions[] = new ActionSubscription($hook, $subscriber, $actionParams);
                    }
                } else {
                    $actions[] = new ActionSubscription($hook, $subscriber, $parameters);
                }
            }
        }

        return $actions;
    }

    private static function getFilterSubscriptions(FilterSubscriber $subscriber)
    {
        $filters = [];

        if ($subscriber->getFilters()) {
            foreach ($subscriber->getFilters() as $hook => $parameters) {
                if (is_array($parameters) && is_array($parameters[0])) {
                    foreach ($parameters[0] as $filterParams) {
                        $filters[] = new FilterSubscription($hook, $subscriber, $filterParams);
                    }
                } else {
                    $filters[] = new FilterSubscription($hook, $subscriber, $parameters);
                }
            }
        }

        return $filters;
    }

    private static function subscribeToAll($subscriptions)
    {
        foreach ($subscriptions as $subscription) {
            if ($subscription instanceof Subscription) {
                $subscription->subscribe();
            }
        }
    }
}
