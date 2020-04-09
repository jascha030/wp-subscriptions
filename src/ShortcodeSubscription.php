<?php

namespace Jascha030\WP\Subscriptions;

use Jascha030\WP\Subscriptions\Exception\NotCallableException;

/**
 * Class ShortcodeSubscription
 *
 * @package Jascha030\WP\Subscriptions
 */
class ShortcodeSubscription extends HookSubscription
{
    /**
     * @throws NotCallableException
     */
    public function subscribe()
    {
        parent::subscribe();

        add_shortcode($this->tag, $this->callable);
    }
}
