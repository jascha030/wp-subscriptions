<?php

namespace Jascha030\WPSI\Subscriber;

/**
 * Interface FilterSubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
interface FilterSubscriber extends Subscriber
{
    /**
     * @return mixed
     */
    public function getFilters();
}
