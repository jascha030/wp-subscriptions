<?php

namespace Jascha030\WP\Subscriptions\Provider;

/**
 * Interface ActionProvider
 *
 * @package Jascha030\WP\Subscriptions\Provider
 */
interface ActionProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public static function getActions();
}
