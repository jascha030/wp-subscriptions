<?php

namespace Jascha030\WPSI\Subscription;

use Jascha030\WPSI\Exception\NotCallableException;

/**
 * Class ShortcodeSubscription
 *
 * @package Jascha030\WPSI\Subscription
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
