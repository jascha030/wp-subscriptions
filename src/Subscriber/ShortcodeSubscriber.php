<?php

namespace Jascha030\WPSI\Subscriber;

/**
 * Interface ShortcodeSubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
interface ShortcodeSubscriber extends Subscriber
{
    /**
     * @return mixed
     */
    public function getShortcodes();
}
