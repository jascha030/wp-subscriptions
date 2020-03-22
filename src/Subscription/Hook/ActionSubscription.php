<?php

namespace Jascha030\WPSI\Subscription;

use Exception;

/**
 * Class ActionHookSubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class ActionSubscription extends HookSubscription implements Unsubscribable
{
    private $priority;

    private $acceptedArguments;

    /**
     * ActionSubscription constructor.
     *
     * @param $tag
     * @param $callable
     * @param int $priority
     * @param int $acceptedArguments
     *
     * @throws Exception
     */
    public function __construct($tag, $callable, $priority = 10, $acceptedArguments = 1)
    {
        parent::__construct($tag, $callable);

        $this->priority = $priority;

        $this->acceptedArguments = $acceptedArguments;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function subscribe()
    {
        parent::subscribe();

        add_action($this->tag, $this->callable, $this->priority, $this->acceptedArguments);
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
            $this->active = false;
        }
    }
}
