<?php

namespace Jascha030\WPSI\Subscription;

use Jascha030\WPSI\Exception\DoesNotImplementSubscriptionException;
use Jascha030\WPSI\Subscriber\ActionSubscriber;
use Jascha030\WPSI\Subscriber\FilterSubscriber;
use Jascha030\WPSI\Subscriber\ShortcodeSubscriber;
use Jascha030\WPSI\Subscriber\Subscriber;

class SubscriptionManager
{
    public static function register(Subscriber $object)
    {
        $s = [];
        $s = array_merge($s, ($object instanceof ActionSubscriber) ? self::getActionSubscriptions($object) : []);
        $s = array_merge($s, ($object instanceof FilterSubscriber) ? self::getFilterSubscriptions($object) : []);
        $s = array_merge($s, ($object instanceof ShortcodeSubscriber) ? self::getShortcodeSubscriptions($object) : []);

        self::subscribeToAll($s);
    }

    private static function getActionSubscriptions(ActionSubscriber $subscriber)
    {
        $actions = [];

        if ($subscriber->getActions()) {
            foreach ($subscriber->getActions() as $hook => $parameters) {
                if (is_array($parameters) && is_array($parameters[0])) {
                    foreach ($parameters[0] as $actionParams) {
                        $actions[] = new ActionHookSubscription($hook, $subscriber, $actionParams);
                    }
                } else {
                    $actions[] = new ActionHookSubscription($hook, $subscriber, $parameters);
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
                        $filters[] = new FilterHookSubscription($hook, $subscriber, $filterParams);
                    }
                } else {
                    $filters[] = new FilterHookSubscription($hook, $subscriber, $parameters);
                }
            }
        }

        return $filters;
    }

    private static function getShortcodeSubscriptions(ShortcodeSubscriber $subscriber)
    {
        $shortcodes = [];

        if ($subscriber->getShortcodes()) {
            foreach ($subscriber->getShortcodes() as $tag => $function) {
                $shortcodes[] = new ShortcodeSubscription($tag, [$subscriber, $function]);
            }
        }

        return $shortcodes;
    }

    private static function subscribeToAll($subscriptions)
    {
        foreach ($subscriptions as $subscription) {
            if ($subscription instanceof Subscription) {
                $subscription->subscribe();
            } else {
                throw new DoesNotImplementSubscriptionException();
            }
        }
    }
}
