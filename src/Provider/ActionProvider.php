<?php

namespace Jascha030\WPOL\Subscription\Provider;

/**
 * Interface ActionProvider
 *
 * @package Jascha030\WPOL\Subscription\Provider
 */
interface ActionProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public static function getActions();
}
