<?php

namespace Jascha030\WPSI\Provider;

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
    public function getFilters();
}
