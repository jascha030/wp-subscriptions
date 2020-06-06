<?php

namespace Jascha030\WP\Subscriptions;

class ShortcodeSubscription extends Subscription
{
    protected function activation()
    {
        call_user_func_array('add_shortcode', $this->data);
    }

    protected function termination()
    {
        call_user_func('remove_shortcode', $this->data['tag']);
    }
}
