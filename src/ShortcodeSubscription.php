<?php

namespace Jascha030\WP\Subscriptions;

/**
 * Class ShortcodeSubscription
 *
 * @package Jascha030\WP\Subscriptions
 */
class ShortcodeSubscription extends Subscription
{
    protected $data = [];

    /**
     * ShortcodeSubscription constructor.
     *
     * @param $tag
     * @param $callable
     */
    public function __construct($tag, $callable)
    {
        $this->data['tag']      = $tag;
        $this->data['callable'] = $callable;

        parent::__construct();
    }

    public function subscribe()
    {
        parent::subscribe();
        add_shortcode($this->data['tag'], $this->data['callable']);
    }
}
