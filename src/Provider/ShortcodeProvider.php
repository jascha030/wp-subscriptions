<?php

namespace Jascha030\WPOL\Subscription\Provider;

/**
 * Interface ShortcodeProvider
 *
 * @package Jascha030\WPOL\Subscription\Provider
 */
interface ShortcodeProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public static function getShortcodes();
}
