<?php

namespace Jascha030\WPSI\Subscription\Provider;

/**
 * Interface ShortcodeProvider
 *
 * @package Jascha030\WPSI\Subscription\Provider
 */
interface ShortcodeProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public static function getShortcodes();
}
