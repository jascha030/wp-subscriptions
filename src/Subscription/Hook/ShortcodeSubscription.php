<?php

namespace Jascha030\WPSI\Subscription;

use Exception;

/**
 * Class ShortcodeSubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class ShortcodeSubscription extends HookSubscription
{
    /**
     * @return void
     * @throws Exception
     */
    public function subscribe()
    {
        parent::subscribe();

        add_shortcode($this->tag, $this->callable);
    }
}
