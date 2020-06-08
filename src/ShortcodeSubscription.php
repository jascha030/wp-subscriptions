<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Provider\SubscriptionProvider;

class ShortcodeSubscription extends Subscription
{
    public static function create(SubscriptionProvider $provider, $context)
    {
        // TODO: Implement create() method.
    }

    protected function activation(): void
    {
        add_shortcode(...$this->data);
    }

    protected function termination(): void
    {
        remove_shortcode($this->data['tag']);
    }
}
