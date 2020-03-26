<?php

namespace Jascha030\WPSI\Provider\StaticProvider;

use Jascha030\WPSI\Provider\SubscriptionProvider;

/**
 * Interface StaticActionProvider
 */
interface StaticActionProvider extends SubscriptionProvider
{
    /**
     * @return mixed
     */
    public static function getStaticActions();
}
