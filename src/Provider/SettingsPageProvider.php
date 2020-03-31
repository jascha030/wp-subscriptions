<?php

namespace Jascha030\WPOL\Subscription\Provider;

interface SettingsPageProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public static function getSettingsPages();
}
