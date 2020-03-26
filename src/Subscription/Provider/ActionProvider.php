<?php

namespace Jascha030\WPSI\Subscription\Provider;

/**
 * Interface ActionProvider
 *
 * @package Jascha030\WPSI\Provider
 */
interface ActionProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public static function getActions();
}
