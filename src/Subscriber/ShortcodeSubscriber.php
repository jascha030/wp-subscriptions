<?php

namespace Jascha030\WPSI\Subscriber;

use Jascha030\WPSI\Subscription\ShortcodeSubscription;

/**
 * class ShortcodeSubscriber
 *
 * @package Jascha030\WPSI\Subscriber
 */
class ShortcodeSubscriber extends Subscriber
{
    protected $shortcodes = [];

    /**
     * @return array
     */
    public function getSubscriptions()
    {
        return $this->shortcodes;
    }

    /**
     * @param $key
     * @param $method
     *
     * @return bool
     */
    public function setSubscription($key, $method)
    {
        $this->shortcodes[$key] = $method;
    }

    /**
     * @return array
     */
    protected function createSubscriptions()
    {
        $shortcodes = [];

        foreach ($this->getSubscriptions() as $tag => $function) {
            $shortcodes[] = new ShortcodeSubscription($tag, [$this, $function]);
        }

        return $shortcodes;
    }
}
