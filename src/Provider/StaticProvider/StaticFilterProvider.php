<?php

namespace Jascha030\WPSI\Provider\StaticProvider;

use Jascha030\WPSI\Provider\SubscriptionProvider;

/**
 * Interface StaticFilterProvider
 *
 * @package Jascha030\WPSI\Provider\StaticProvider
 */
interface StaticFilterProvider extends SubscriptionProvider
{
    /**
     * @return mixed
     */
    public static function getStaticFilters();
}
