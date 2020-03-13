<?php

namespace Jascha030\WPSI\Subscriber;

/**
 * Interface ActionSubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
interface ActionSubscriber extends Subscriber
{
    /**
     * @return mixed
     */
    public function getActions();
}
