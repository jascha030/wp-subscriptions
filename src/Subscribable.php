<?php

namespace Jascha030\WP\Subscriptions;

/**
 * Interface Subscribable
 *
 * @package Jascha030\WP\Subscriptions
 */
interface Subscribable
{
    /**
     * @return mixed
     */
    public function subscribe();

    /**
     * @return mixed
     */
    public function unsubscribe();
}
