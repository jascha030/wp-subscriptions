<?php

namespace Jascha030\WPSI\Provider;

/**
 * Interface ShortcodeProvider
 *
 * @package Jascha030\WPSI\Provider
 */
interface ShortcodeProvider extends SubscriptionProvider
{
    /**
     * @return array
     */
    public function getShortcodes();
}
