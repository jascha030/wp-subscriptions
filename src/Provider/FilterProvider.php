<?php

namespace Jascha030\WPOL\Subscription\Provider;

/**
 * Interface FilterProvider
 *
 * @package Jascha030\WPOL\Subscription\Provider
 */
interface FilterProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public static function getFilters();
}
