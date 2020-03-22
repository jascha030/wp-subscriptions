<?php

namespace Jascha030\WPSI\Subscription;

use Exception;

/**
 * Class HookSubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class HookSubscription extends Subscription
{
    protected $tag;

    protected $callable;

    /**
     * HookSubscription constructor.
     *
     * @param $tag
     * @param $callable
     *
     * @throws Exception
     */
    public function __construct($tag, $callable)
    {
        if (! is_callable($callable)) {
            throw new Exception("variable is not a valid callable");
        }

        $this->tag = $tag;

        $this->callable = $callable;
    }

    public function subscribe()
    {
        if (! is_callable($this->callable) && ! function_exists($this->callable)) {
            throw new Exception("Invalid callable"); //Todo: create Exception.
        }
        
        parent::subscribe();
    }
}
