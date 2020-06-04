<?php

namespace Jascha030\WP\Subscriptions;

/**
 * Interface Subscribable
 *
 * @package Jascha030\WP\Subscriptions
 */
interface Subscribable
{
    public function subscribe();

    public function unsubscribe();
}
