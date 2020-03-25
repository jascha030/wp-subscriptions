<?php

namespace Jascha030\WPSI\Provider\StaticProvider;

/**
 * Interface StaticFilterProvider
 *
 * @package Jascha030\WPSI\Provider\StaticProvider
 */
interface StaticFilterProvider extends StaticSubscriptionProvider
{
    /**
     * @return mixed
     */
    public static function getStaticFilters();
}
