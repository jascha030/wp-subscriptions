<?php

namespace Jascha030\WPSI\Subscription;

use Jascha030\WPSI\Exception\InvalidFunctionException;
use Jascha030\WPSI\Exception\InvalidMethodException;

/**
 * Class ShortcodeSubscription
 *
 * @package Jascha030\WPSI\Subscription
 */
class ShortcodeSubscription implements Subscribable
{
    private $tag;

    private $function;

    /**
     * ShortcodeSubscription constructor.
     *
     * @param string $tag
     * @param $function
     */
    public function __construct(string $tag, $function)
    {
        $this->tag = $tag;

        $this->function = $function;
    }

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
