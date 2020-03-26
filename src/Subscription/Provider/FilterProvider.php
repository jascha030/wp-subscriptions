<?php

namespace Jascha030\WPSI\Subscription\Provider;

/**
 * Interface FilterProvider
 *
 * @package Jascha030\WPSI\Provider
 */
interface FilterProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public static function getFilters();
}
