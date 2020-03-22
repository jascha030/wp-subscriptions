<?php

namespace Jascha030\WPSI\Subscription;

use Exception;

/**
 * Class ActionHookSubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class ActionSubscription extends HookableSubscription
{
    /**
     * @return void
     * @throws Exception
     */
    public function subscribe()
    {
        if ($this->isActive()) {
            throw new Exception("Already subscribed"); //Todo: Make exception class.
        } else {
            add_action($this->tag, $this->callable, $this->priority, $this->acceptedArguments);
            $this->active = true;
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    public function unsubscribe()
    {
        if ($this->isActive()) {
            throw new Exception("Can't unsubscribe before subscribing"); //Todo: make exception class.
        } else {
            remove_action($this->tag, $this->callable, $this->priority, $this->acceptedArguments);
        }
    }
}
