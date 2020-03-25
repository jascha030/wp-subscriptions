<?php

namespace Jascha030\WPSI\Provider\StaticProvider;

/**
 * Interface StaticActionProvider
 */
interface StaticActionProvider extends StaticSubscriptionProvider
{
    /**
     * @return mixed
     */
    public static function getStaticActions();
}
