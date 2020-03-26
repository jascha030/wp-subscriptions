<?php

namespace Jascha030\WPOL\Subscription;

use Jascha030\WPOL\Subscription\Exception\NotCallableException;

/**
 * Class ShortcodeSubscription
 *
 * @package Jascha030\WPOL\Subscription
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
