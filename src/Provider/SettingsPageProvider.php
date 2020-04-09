<?php

namespace Jascha030\WP\Subscriptions\Provider;

interface SettingsPageProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public static function getSettingsPages();
}
