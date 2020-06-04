<?php

namespace Jascha030\WP\Subscriptions;

class ShortcodeSubscription extends Subscription
{
    protected $data = [];

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
