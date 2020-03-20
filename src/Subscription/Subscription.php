<?php

namespace Jascha030\WPSI\Subscription;

/**
 * Interface Subscription
 *
 * @package Jascha030\WPSI\Subscription
 */
interface Subscription
{
    /**
     * Subscription constructor.
     *
     * @param array $data
     */
    public function __construct(array $data);

    public function subscribe();
}
