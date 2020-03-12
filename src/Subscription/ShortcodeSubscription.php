<?php

namespace Jascha030\WPSI\Subscription;

use Jascha030\WPSI\Exception\InvalidFunctionException;
use Jascha030\WPSI\Exception\InvalidMethodException;

class ShortcodeSubscription implements Subscription
{
    private $tag;

    private $function;

    public function subscribe()
    {
        if (is_array($this->function)) {
            if (method_exists($this->function[0], $this->function[1])) {
                add_shortcode($this->tag, $this->function);
            } else {
                throw new InvalidMethodException();
            }
        } elseif (function_exists($this->function)) {
            add_shortcode($this->tag, $this->function);
        } else {
            throw new InvalidFunctionException();
        }
    }
}
