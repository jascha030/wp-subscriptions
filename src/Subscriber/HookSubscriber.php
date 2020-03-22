<?php

use Jascha030\WPSI\Provider\ActionProvider;
use Jascha030\WPSI\Provider\FilterProvider;
use Jascha030\WPSI\Provider\SubscriptionProvider;
use Jascha030\WPSI\Subscription\Hook\ActionSubscription;
use Jascha030\WPSI\Subscription\Hook\FilterSubscription;

/**
 * Class ActionSubscriber
 */
class HookSubscriber implements Subscriber
{
    /**
     * @param SubscriptionProvider $provider
     *
     * @return void
     * @throws Exception
     */
    public static function createSubscriptions(SubscriptionProvider $provider)
    {
        $data = [];
        $subscriptionType = null;

        if (!$provider instanceof ActionProvider && !$provider instanceof FilterProvider) {
            throw new Exception("Wrong provider type"); //Todo: create exception class.
        }

        if ($provider instanceof ActionProvider) {
            $data = $provider->getActions();
            $subscriptionType = ActionSubscription::class;
        }

        if ($provider instanceof FilterProvider) {
            $data = $provider->getFilters();
            $subscriptionType = FilterSubscription::class;
        }

        foreach ($data as $name => $parameters) {
            if (is_array($parameters) && is_array($parameters[0])) {
                foreach ($parameters[0] as $actionParams) {
                    self::createSubscription($provider, $name, $actionParams, $subscriptionType);
                }
            } else {
                self::createSubscription($provider, $name, $parameters, $subscriptionType);
            }
        }
    }

    /**
     * @param $provider
     * @param $tag
     * @param $parameters
     *
     * @return bool|ActionSubscription
     * @throws Exception
     */
    private static function createSubscription($provider, $tag, $parameters, $type)
    {
        if (is_string($parameters)) {
            $subscription = new $type($tag, [$provider, $parameters]);

        } elseif (is_array($parameters) && isset($parameters[0])) {

            $subscription = new $type($tag, [$provider, $parameters[0]], isset($parameters[1]) ?
                $parameters[1] : 10, isset($parameters[2]) ? $parameters[2] : 1);
        }

        return (isset($subscription)) ? $subscription : false;
    }
}
