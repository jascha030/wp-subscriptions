<?php

use Jascha030\WPSI\Provider\SubscriptionProvider;

/**
 * Interface Subscriber
 */
interface Subscriber
{
    /**
     * @param SubscriptionProvider $provider
     *
     * @return mixed
     */
    public static function createSubscriptions(SubscriptionProvider $provider);
}
