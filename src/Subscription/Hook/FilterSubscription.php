<?php

namespace Jascha030\WPSI\Subscription;

use Exception;

/**
 * Class FilterSubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class FilterSubscription extends HookSubscription
{
    private $priority;

    private $acceptedArguments;

    /**
     * FilterSubscription constructor.
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

        add_filter($this->tag, $this->callable, $this->priority, $this->acceptedArguments);
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
            remove_filter($this->tag, $this->callable, $this->priority, $this->acceptedArguments);
        }
    }
}
